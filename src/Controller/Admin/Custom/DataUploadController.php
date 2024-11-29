<?php

namespace App\Controller\Admin\Custom;

use App\Form\Admin\DataUploadType;
use App\Repository\BaseCategoryRepository;
use App\Repository\BaseSubcategoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Repository\WebsiteRepository;
use App\Service\CategoryProcessor;
use App\Service\ProductProcessor;
use Doctrine\DBAL\SQL\Parser\Exception;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DataUploadController extends AbstractController
{
    #[Route('/admin/upload-data', 'app_admin_data_upload')]
    public function uploadFile(Request $request, AdminUrlGenerator $adminUrlGenerator, WebsiteRepository $websiteRepository): Response
    {
        $websites = [];
        foreach ($websiteRepository->findAll() as $website){
            $websites[] = $website->getWebsiteName();
        }
        $forms = [];
        $fileNames = [];
        foreach ($websites as $website) {
            $url = $adminUrlGenerator->setRoute('app_admin_data_upload')->set('website', $website)->generateUrl();
            $form = $this->createForm(DataUploadType::class, null ,[
                'action' => $url
            ]);
            $forms[$website] = $form->createView();

            $folderPath = $this->getParameter('kernel.project_dir').'/src/Data/'.$website;
            if(!is_dir($folderPath)){
                mkdir($folderPath, 0755, true);
            }
            $finder = new Finder();
            $files = $finder->files()->in($folderPath);
            $fileNames[$website] = [];
            foreach ($files as $file) {
                $fileNames[$website][] = $file->getFilename();
            }

            if($request->query->get('website') === $website){
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    $uploadedFiles = $form['file']->getData();

                    foreach ($uploadedFiles as $uploadedFile){
                        $destination = $folderPath;
                        $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $newFileName = $originalFileName.'.'.$uploadedFile->guessExtension();

                        $uploadedFile->move($destination, $newFileName);
                    }
                    $targetUrl = $adminUrlGenerator->setRoute('app_admin_data_upload')->generateUrl();
                    return $this->redirect($targetUrl);
                }
            }
        }

        return $this->render('admin/file-upload.html.twig', [
            'forms' => $forms,
            'files' => $fileNames
        ]);
    }
    #[Route(path: '/admin/delete-file/{website}/{name}',name: 'app_admin_file_delete', methods: 'DELETE')]
    public function deleteFile(string $name, string $website, AdminUrlGenerator $adminUrlGenerator)
    {
        $folderPath = $this->getParameter('kernel.project_dir').'/src/Data/'.$website;
        $file = $folderPath.'/'.$name;
        if(file_exists($file)){
            unlink($file);
        }

        $targetUrl = $adminUrlGenerator->setRoute('app_admin_data_upload')->generateUrl();
        return $this->redirect($targetUrl);
    }

    #[Route('/admin/get-files', name: 'app_admin_get_files', methods: ['POST'])]
    public function getFiles(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        $website = $requestBody['website'];

        $folderPath = $this->getParameter('kernel.project_dir') . '/src/Data/' . $website . '/';

        if (!is_dir($folderPath)) {
            return new JsonResponse(['error' => 'Directory not found'], 404);
        }

        $finder = new Finder();
        $files = $finder->files()->in($folderPath);

        $fileNames = [];
        foreach ($files as $file) {
            $fileNames[] = $file->getFilename();
        }

        return new JsonResponse($fileNames, 200);
    }

    #[Route('/admin/process-categories', name: 'app_admin_category_process', methods: 'POST')]
    public function processCategories(CategoryProcessor $categoryProcessor, Request $request)
    {
        $requestBody = json_decode($request->getContent(), true);
        $website = $requestBody['website'];
        $fileName = $requestBody['fileName'];

        $filePath = $this->getParameter('kernel.project_dir') . '/src/Data/' . $website . '/' . $fileName;
        if(!file_exists($filePath)){
            return new JsonResponse(['error' => 'File not found' . $filePath], 404);
        }
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        if (!$data) {
            return new JsonResponse(['error' => 'No data found in ' . $fileName], 404);
        }
        $categoryProcessor->processCategories($data);
        return new JsonResponse(['message' => $fileName . ' processed successfully'], 200);

    }

    #[Route('/admin/process-data', name: 'app_admin_data_process', methods: 'POST')]
    public function processData(
        ProductProcessor $productProcessor,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        WebsiteRepository $websiteRepository
    ): Response
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $website = $requestBody['website'];
            $fileName = $requestBody['fileName'];

            $filePath = $this->getParameter('kernel.project_dir') . '/src/Data/' . $website . '/' . $fileName;
            if(!file_exists($filePath)){
                return new JsonResponse(['error' => 'File not found' . $filePath], 404);
            }

            $existingProductsByWebsiteId = $productRepository->findAllProductsWebsiteId($website);
            $deliveryPriceRoles = $websiteRepository->findOneBy(['websiteName' => $website])->getWebsiteDeliveryRoles();
            $formatedPriceRoles = [];
            foreach ($deliveryPriceRoles as $deliveryPriceRole) {
                $formatedPriceRoles[] = [
                    'min' => $deliveryPriceRole->getMin(),
                    'max' => $deliveryPriceRole->getMax(),
                    'deliveryPrice' => $deliveryPriceRole->getDeliveryPrice(),
                ];
            }
            $choiceProductsArray = [];


            $jsonData = file_get_contents($filePath);
            $data = json_decode($jsonData, true);
            $batchLimit = 50;
            $batchCounter = 0;
            foreach ($data as $row) {
                if ($row['is-product-choice']) {
                    $choiceProductsArray[] = $row;
                    continue;
                }
                if (!$row['new-price'] || !$row['old-price'] || !$row['discount-percent'] || !$row['images']) continue;

                if (!in_array($row['website-id'], $existingProductsByWebsiteId)) {
                    try {
                        $productProcessor->createNewProduct($row, $formatedPriceRoles);
                    } catch (\Exception $exception){
                        dump('Exception at creating product: ' .$exception->getMessage());
                    }

                } else {
                    try {
                        $existingProduct = $productRepository->findOneBy(['websiteId' => $row['website-id']]);
                        $existingProduct = $productProcessor->checkIfProductUpdated($row, $existingProduct);
                        $existingProduct->setForDelete(false);
                        $entityManager->persist($existingProduct);
                    } catch (\Exception $exception){
                        dump('Exception at excisting product: ' . $exception->getMessage());
                    }

                }
                if($batchCounter++ >= $batchLimit) {
                    try{

                        dump('Batch ' . $batchCounter . ' processed');
                        $entityManager->flush();
                        $entityManager->clear();
                        $batchCounter = 0;
                    } catch (\Exception $exception) {
                        dump('We got exception: ' . $exception->getMessage());
                    }

                }
            }

            dump('Memory after 1 file processed: ' . memory_get_usage());


            dump('Memory after all files processed: ' . memory_get_usage());
//            $productRepository->deleteProductChoicesForMarkedProducts($website);
            try {
                $productRepository->deleteALlMissingProducts($website);
            } catch (Exception $exception){
                dump('We got exception at deleting products: ' . $exception->getMessage());
            }
            try {
                $productRepository->refreshForDeleteField();
            } catch (Exception $exception){
                dump('We got exception at refresh delete fields: ' . $exception->getMessage());
            }


//            $productProcessor->createProductChoices($choiceProductsArray);
//            $productProcessor->createProductOptions();
//            if (!$productRepository->deleteALlMissingProducts($website)) {
//                $productChoiceRepository->deleteALlMissingProductChoices($website);
//            }
//            $productRepository->refreshForDeleteField();
//            $productChoiceRepository->refreshForDeleteField();
            return new JsonResponse($choiceProductsArray, 200);
        } catch (Exception $error){
            dump($error);
        }

//        $this->addFlash('success', "Data updated successfully");
        return new JsonResponse('Fail', 400);
    }

}