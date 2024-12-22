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
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DataUploadController extends AbstractController
{
    #[Route('/admin/upload-data', 'app_admin_data_upload')]
    public function uploadFile(Request $request, AdminUrlGenerator $adminUrlGenerator, WebsiteRepository $websiteRepository, FilesystemOperator $s3Storage): Response
    {
        $websites = $websiteRepository->findAll();
        $associatedWebsites = [];
        foreach ($websites as $website) {
            $associatedWebsites[$website->getWebsiteName()] = $website;
        }
        $forms = [];
        $fileNames = [];
        foreach ($websites as $website) {
            $websiteName = $website->getWebsiteName();
            $url = $adminUrlGenerator->setRoute('app_admin_data_upload')->set('website', $websiteName)->generateUrl();
            $form = $this->createForm(DataUploadType::class, null ,[
                'action' => $url
            ]);
            $forms[$websiteName] = $form->createView();

            $fileNames[$websiteName] = [];
            foreach ($s3Storage->listContents("data/$websiteName", false) as $file) {
                if ($file->isFile()) { // Check if it's a file
                    $fileNames[$websiteName][] = basename($file->path()); // Use path() to get the full file path
                }
            }

            if($request->query->get('website') === $websiteName){
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    $uploadedFiles = $form['file']->getData();

                    foreach ($uploadedFiles as $uploadedFile){
                        $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $newFileName = $originalFileName.'.'.$uploadedFile->guessExtension();

                        $s3Storage->write("data/$websiteName/$newFileName", file_get_contents($uploadedFile->getPathname()));
                    }
                    $targetUrl = $adminUrlGenerator->setRoute('app_admin_data_upload')->generateUrl();
                    return $this->redirect($targetUrl);
                }
            }
        }

        return $this->render('admin/file-upload.html.twig', [
            'forms' => $forms,
            'files' => $fileNames,
            'websites' => $associatedWebsites
        ]);
    }
    #[Route(path: '/admin/delete-file/{website}/{name}', name: 'app_admin_file_delete', methods: ['DELETE'])]
    public function deleteFile(
        string $name,
        string $website,
        AdminUrlGenerator $adminUrlGenerator,
        FilesystemOperator $s3Storage
    ) {
        $filePath = "data/$website/$name";

        // Delete the file from S3
        if ($s3Storage->fileExists($filePath)) {
            $s3Storage->delete($filePath);
        }

        $targetUrl = $adminUrlGenerator->setRoute('app_admin_data_upload')->generateUrl();
        return $this->redirect($targetUrl);
    }

    #[Route('/admin/get-files', name: 'app_admin_get_files', methods: ['POST'])]
    public function getFiles(Request $request, FilesystemOperator $s3Storage): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        $website = $requestBody['website'];

        $folderPath = "data/$website/";

        try {
            $files = $s3Storage->listContents($folderPath);

            $fileNames = [];
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $fileNames[] = basename($file->path());
                }
            }

            return new JsonResponse($fileNames, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to fetch files: ' . $e->getMessage()], 500);
        }
    }


    #[Route('/admin/process-categories', name: 'app_admin_category_process', methods: ['POST'])]
    public function processCategories(
        CategoryProcessor $categoryProcessor,
        Request $request,
        FilesystemOperator $s3Storage
    ): JsonResponse {
        $requestBody = json_decode($request->getContent(), true);
        $website = $requestBody['website'];
        $fileName = $requestBody['fileName'];

        $filePath = "data/$website/$fileName";

        if (!$s3Storage->fileExists($filePath)) {
            return new JsonResponse(['error' => 'File not found: ' . $filePath], 404);
        }

        $jsonData = $s3Storage->read($filePath);
        $data = json_decode($jsonData, true);

        if (!$data) {
            return new JsonResponse(['error' => 'No data found in ' . $fileName], 404);
        }

        $categoryProcessor->processCategories($data);
        return new JsonResponse(['message' => $fileName . ' processed successfully'], 200);
    }


    #[Route('/admin/process-data', name: 'app_admin_data_process', methods: ['POST'])]
    public function processData(
        ProductProcessor $productProcessor,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        WebsiteRepository $websiteRepository,
        FilesystemOperator $s3Storage
    ): Response {
        $requiredKeys = [
            'is-product-choice',
            'website',
            'website-url',
            'website-id',
            'product-url',
            'title',
            'old-price',
            'new-price',
            'discount-percent',
            'images',
            'categories',
            'description-html',
        ];
        $exceptionLogs = [];
        $newProducts = 0;
        $existingProducts = 0;

        $requestBody = json_decode($request->getContent(), true);
        $website = $requestBody['website'];
        $fileName = $requestBody['fileName'];

        $filePath = "data/$website/$fileName";
        if (!$s3Storage->fileExists($filePath)) {
            return new JsonResponse(['error' => 'File not found: ' . $filePath], 404);
        }

        $jsonData = $s3Storage->read($filePath);
        $data = json_decode($jsonData, true);

        if (!$data) {
            return new JsonResponse(['error' => 'No data found in ' . $fileName], 404);
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

        $batchLimit = 50;
        $batchCounter = 0;
        foreach ($data as $index => $row) {
            if ($row['is-product-choice']) {
                $choiceProductsArray[] = $row;
                continue;
            }

            $missingKeys = array_diff($requiredKeys, array_keys($row));

            if (!empty($missingKeys)) {
                $exceptionLogs[] = [
                    "message" => "Missing required keys: " . implode(', ', $missingKeys) . " - Error occurred at record " . $index
                ];
                continue;
            }

            if (!$row['new-price'] || !$row['old-price'] || !$row['discount-percent'] || !$row['images']) {
                continue;
            }

            if (!in_array($row['website-id'], $existingProductsByWebsiteId)) {
                $productProcessor->createNewProduct($row, $formatedPriceRoles);
                $newProducts++;
            } else {
                $existingProduct = $productRepository->findOneBy(['websiteId' => $row['website-id']]);
                $existingProduct = $productProcessor->checkIfProductUpdated($row, $existingProduct);
                $existingProduct->setForDelete(false);
                $entityManager->persist($existingProduct);
                $existingProducts++;
            }

            if ($batchCounter++ >= $batchLimit) {
                $entityManager->flush();
                $batchCounter = 0;
            }
        }

        $entityManager->flush(); // Flush products stacked in the batch

        return new JsonResponse(
            [
                'choiceProductsArray' => $choiceProductsArray,
                'exceptionLogs' => $exceptionLogs,
                'newProducts' => $newProducts,
                'existingProducts' => $existingProducts
            ],
            200
        );
    }


    #[Route('/admin/process-choices', name: 'app_admin_choices_process', methods: 'POST')]
    public function processChoices(Request $request, ProductProcessor $productProcessor){
        $requestBody = json_decode($request->getContent(), true);
        $choiceProductsArray = $requestBody['choiceProducts'];

        $result = $productProcessor->createProductChoices($choiceProductsArray);
        $productProcessor->createProductOptions();

        return new JsonResponse([
            'message' => 'Choices successfully processed',
            'newChoices' => $result['newChoices'],
            'existingChoices' => $result['existingChoices'],
            'exceptionLogs' => $result['exceptionLogs']
        ], 200);
    }

    #[Route('/admin/delete-missing-products-and-choices', name: 'app_admin_delete_products_and_choices', methods: 'POST')]
    public function deleteMissingProductsAndChoices(ProductRepository $productRepository, Request $request, WebsiteRepository $websiteRepository, EntityManagerInterface $entityManager){
        $requestBody = json_decode($request->getContent(), true);
        $websiteName = $requestBody['website'];

        $website = $websiteRepository->findOneBy(['websiteName' => $websiteName]);
        $website->setProcessedAt(new \DateTimeImmutable('now'));
        $entityManager->persist($website);
        $entityManager->flush();
        $productsForDeleteLeftBefore = $productRepository->getNumberOfProductsForDelete($websiteName);
        $result = $productRepository->deleteALlMissingProducts($websiteName);
        $productsForDeleteLeft = $productRepository->getNumberOfProductsForDelete($websiteName);
        if($productsForDeleteLeft === 0){
            $productRepository->refreshForDeleteField();
        }


        return new JsonResponse([
            'removedProducts' => $result['removedProducts'],
            'removedChoices' => $result['removedChoices'],
            'productsForDeleteLeft' => $productsForDeleteLeft,
            'productsForDeleteLeftBefore' => $productsForDeleteLeftBefore
        ], 200);
    }
}