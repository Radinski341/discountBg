<?php

namespace App\Controller\Admin\Custom;

use App\Entity\Website;
use App\Form\Admin\DataUploadType;
use App\Repository\BaseCategoryRepository;
use App\Repository\BaseSubcategoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Repository\WebsiteRepository;
use App\Service\CategoryProcessor;
use App\Service\DataFinder;
use App\Service\ProductProcessor;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\VarDumper\Cloner\Data;

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

    #[Route('/admin/process-data', name: 'app_admin_data_process', methods: 'POST')]
    public function processData(
        CategoryProcessor $categoryProcessor,
        BaseCategoryRepository $baseCategoryRepository,
        BaseSubcategoryRepository $baseSubcategoryRepository,
        ProductProcessor $productProcessor,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SubCategoryRepository $subCategoryRepository,
        ProductChoiceRepository $productChoiceRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        WebsiteRepository $websiteRepository,
    ): Response
    {
        $data = json_decode($request->getContent(), true);
        $website = $data['website'];

        $folderPath = $this->getParameter('kernel.project_dir') . '/src/Data/'.$website.'/';
        $finder = new Finder();
        $files = $finder->files()->in($folderPath);

        foreach ($files as $file) {
            $jsonData = file_get_contents($file->getRealPath());
            $data = json_decode($jsonData, true);

            if ($data) {
                $categoryProcessor->processCategories($data);
            }
        }

        $existingProductsByWebsiteId = $productRepository->findAllProductsWebsiteId($website);

        $allCategories = $categoryRepository->findAll();
        $allSubCategories = $subCategoryRepository->findAll();
        $allBaseCategories = $baseCategoryRepository->findAll();
        $allBaseSubcategories = $baseSubcategoryRepository->findAll();
        $allProducts = $productRepository->findBy(['websiteName' => $website]);
        $allProductChoices = $productChoiceRepository->findBy(['websiteName' => $website]);
        $deliveryPriceRoles = $websiteRepository->findOneBy(['websiteName' => $website])->getWebsiteDeliveryRoles();
        $formatedPriceRoles = [];
        foreach ($deliveryPriceRoles as $deliveryPriceRole){
            $formatedPriceRoles[] = [
                'min' => $deliveryPriceRole->getMin(),
                'max' => $deliveryPriceRole->getMax(),
                'deliveryPrice' => $deliveryPriceRole->getDeliveryPrice(),
            ];
        }
        $choiceProductsArray = [];

        foreach ($files as $fileName) {
            $jsonData = file_get_contents($fileName->getRealPath());
            $data = json_decode($jsonData, 1);
            foreach ($data as $row) {
                if ($row['is-product-choice']) {
                    $choiceProductsArray[] = $row;
                    continue;
                }
                if(!$row['new-price'] || !$row['old-price'] || !$row['discount-percent'] || !$row['images']) continue;

                if (!in_array($row['website-id'], $existingProductsByWebsiteId)) {
                    $productProcessor->createNewProduct($row, $allCategories, $allSubCategories, $allBaseCategories, $allBaseSubcategories, $formatedPriceRoles);
                } else {
                    $existingProduct = $productProcessor->getExistingProduct($row['website-id'], $allProducts);
                    $existingProduct = $productProcessor->checkIfProductUpdated($row, $existingProduct);
                    $existingProduct->setForDelete(false);
                    $entityManager->persist($existingProduct);
                }
            }
            $entityManager->flush();
        }

        $productsForDelete = $productRepository->findBy(['forDelete' => true, 'websiteName' => $website]);
        foreach ($productsForDelete as $product) {
            foreach ($product->getProductChoices() as $productChoice) {
                $entityManager->remove($productChoice);
            }
        }
        $entityManager->flush();
        $productProcessor->createProductChoices($choiceProductsArray, $allProductChoices);
        $productProcessor->createProductOptions();
        if (!$productRepository->deleteALlMissingProducts($website)) {
            $productChoiceRepository->deleteALlMissingProductChoices($website);
        }
        $productRepository->refreshForDeleteField();
        $productChoiceRepository->refreshForDeleteField();


        $this->addFlash('success', "Data updated successfully");
        return new Response('Products updated successfully');
    }

}