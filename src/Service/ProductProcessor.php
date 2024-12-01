<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductChoice;
use App\Entity\SubCategory;
use App\Repository\BaseCategoryRepository;
use App\Repository\BaseSubcategoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductProcessor
{
    private EntityManagerInterface $entityManager;

    private ProductRepository $productRepository;
    private ProductChoiceRepository $productChoiceRepository;
    private CategoryProcessor $categoryProcessor;
    private BaseCategoryRepository $baseCategoryRepository;
    private CategoryRepository $categoryRepository;
    private SubCategoryRepository $subCategoryRepository;
    private BaseSubcategoryRepository $baseSubCategoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryProcessor $categoryProcessor,
        ProductRepository $productRepository,
        ProductChoiceRepository $productChoiceRepository,
        BaseCategoryRepository $baseCategoryRepository,
        BaseSubCategoryRepository $baseSubCategoryRepository,
        CategoryRepository $categoryRepository,
        SubCategoryRepository $subCategoryRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->productChoiceRepository = $productChoiceRepository;
        $this->categoryProcessor = $categoryProcessor;
        $this->baseCategoryRepository = $baseCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->baseSubCategoryRepository = $baseSubCategoryRepository;
    }

    private function getCategoryAndSubcategory($allCategoryLevels): array
    {
        $allCategoryLevelsArray = explode("|", $allCategoryLevels);
        $category = $allCategoryLevelsArray[1];
        $subCategory = null;
        if(count($allCategoryLevelsArray) > 2){
            $subCategory = $allCategoryLevelsArray[2];
        }

        return[
            'category' => $category,
            'subCategory' => $subCategory
        ];
    }

    private function findCategory($allCategoryLevels): Category
    {
        $categoryTitle = $this->getCategoryAndSubcategory($allCategoryLevels)['category'];
        $baseCategory = $this->baseCategoryRepository->findOneBy(['replaceCategory' => $categoryTitle]);
        if($baseCategory){
            return $this->categoryRepository->findOneBy(['title' => $baseCategory->getTitle()]);
        }

        return $this->categoryRepository->findOneBy(['title' => $categoryTitle]);
    }

    private function findSubCategory($allCategoryLevels): SubCategory
    {
        $category = $this->findCategory($allCategoryLevels);
        $categoryTitle = $category->getTitle();
        $processingRecordCategoriesAndSubcategories = $this->getCategoryAndSubcategory($allCategoryLevels);
        $processingRecordSubCategory = $processingRecordCategoriesAndSubcategories['subCategory'];
        $baseSubcategory = $this->baseSubCategoryRepository->findOneBy(['replaceSubcategory' => $processingRecordSubCategory, 'category' => $categoryTitle]);
        if($baseSubcategory){
            return $this->subCategoryRepository->findOneBy(['title' => $baseSubcategory->getTitle()]);
        }
        return $this->subCategoryRepository->findOneBy(['title' => $processingRecordSubCategory, 'category' => $category]);
    }


    private function newPriceLogic(float $oldPrice, float $discountPercent): float
    {
        return ceil($oldPrice * (1 - $discountPercent / 100)) - 0.01;
    }

    private function newDiscountPercentLogic(
     float $originalPrice,
     float $originalDiscountPercent,
     float $baseReduction = 5,
     float $priceThreshold = 500,
     float $additionalReductionFactor = 0.05
    ): float {
        // Step 1: Calculate additional reduction based on price and factor
        $priceReduction = ($originalPrice / $priceThreshold) * $additionalReductionFactor * 100;

        // Step 2: Calculate new discount percent with base reduction and price-weighted reduction
        $newDiscountPercent = $originalDiscountPercent - $baseReduction - $priceReduction;

        // Step 3: Ensure the discount is not negative or below reasonable values (e.g., cap at 5%)
        if ($newDiscountPercent < 1) {
            $newDiscountPercent = 1;  // Ensure at least a 1% discount
        } elseif ($newDiscountPercent > $originalDiscountPercent) {
            $newDiscountPercent = $originalDiscountPercent;  // Don't increase discount beyond the original
        }
        return ceil($newDiscountPercent);
    }

    private function newDeliveryPriceLogic($deliveryPriceRoles, $productPrice): float
    {
        foreach ($deliveryPriceRoles as $priceRole) {
            if ($productPrice >= $priceRole['min'] && $productPrice < $priceRole['max']) {
                return $priceRole['deliveryPrice'];
            }
        }

        return 0;
    }

    public function createNewProduct($row, $deliveryPriceRoles): void
    {
        try {
            $newDiscountPercent = $this->newDiscountPercentLogic($row['new-price'], $row['discount-percent']);
            $product = new Product();
            $product->setTitle($row['title']);
            $product->setWebsiteName($row['website']);
            $product->setWebsiteUrl($row['website-url']);
            $product->setWebsiteId($row['website-id']);
            $product->setCategory($this->findCategory($row['categories']));
            $product->setSubCategory($this->findSubCategory($row['categories']));
            $product->setImages($row['images']);
            $product->setNewPrice($this->newPriceLogic($row['old-price'], $newDiscountPercent));
            $product->setOldPrice($row['old-price']);
            $product->setDeliveryPrice($this->newDeliveryPriceLogic($deliveryPriceRoles, $row['new-price']));
            $product->setOriginalDiscountPrice($row['new-price']);
            $product->setOriginalDiscountPercent($row['discount-percent']);
            $product->setDiscountPercent($newDiscountPercent);
            $product->setOptionTypes('');
            $product->setOptions('');
            $product->setProductUrl($row['product-url']);
            $product->setDescription($row['description-html']);
            $product->setForDelete(false);
            $product->setIsActive(true);
            $product->setCreatedAt(new \DateTime());
            $product->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($product);
        } catch (\Exception $exception){
            dump('Product creation error: ' . $exception->getMessage());
        }


    }


    public function getExistingProductChoice($websiteId)
    {
        return $this->productChoiceRepository->findOneBy(['websiteId' => $websiteId]);
    }

    public function checkIfProductUpdated($currentData, Product $product): Product
    {
        if(
            $currentData['new-price'] !== $product->getOriginalDiscountPrice() ||
            $currentData['discount-percent'] !== $product->getOriginalDiscountPercent() ||
            $currentData['old-price'] !== $product->getOldPrice()
        ){
            $newDiscountPercent = $this->newDiscountPercentLogic($currentData['new-price'], $currentData['discount-percent']);
            $product->setOldPrice($currentData['old-price']);
            $product->setOriginalDiscountPercent($currentData['discount-percent']);
            $product->setOriginalDiscountPrice($currentData['new-price']);
            $product->setNewPrice($this->newPriceLogic($currentData['old-price'], $newDiscountPercent));
            $product->setDiscountPercent($newDiscountPercent);
            $product->setUpdatedAt(new \DateTime());
        }
        return $product;
    }

    public function createProductChoices($productChoices): array
    {
        $newChoices = 0;
        $existingChoices = 0;
        $exceptionLogs = [];
        $existingProductChoices = $this->productChoiceRepository->findAllProductsWebsiteId();
        foreach ($productChoices as $index => $productChoice){
            $requiredKeys = [
                'is-product-choice',
                'website',
                'parent-website-id',
                'website-id',
                'product-url',
                'title',
                'old-price',
                'new-price',
                'discount-percent',
                'images'
            ];
            $missingKeys = array_diff($requiredKeys, array_keys($productChoice));

            if (!empty($missingKeys)) {
                $exceptionLogs[] = ["message" => "Missing required keys: " . implode(', ', $missingKeys) . " - Error occurred at record " . $index];
                continue;
            }
            $productParent = $this->productRepository->findOneBy(['websiteId' => $productChoice['parent-website-id']]);
            $newDiscountPercent = $this->newDiscountPercentLogic($productChoice['new-price'], $productChoice['discount-percent']);
            if($productParent ){
                if(!in_array($productChoice['website-id'], $existingProductChoices)){
                    $product = new ProductChoice();
                    $product->setProduct($productParent);
                    $product->setTitle($productChoice['title']);
                    $product->setWebsiteId($productChoice['website-id']);
                    $product->setWebsiteName($productChoice['website']);
                    $product->setProductUrl($productChoice['product-url']);
                    $product->setOptionType($productChoice['option-type']);
                    $product->setOptionValue($productChoice['option']);
                    $product->setOldPrice($productChoice['old-price']);
                    $product->setOriginalDiscountPrice($productChoice['new-price']);
                    $product->setNewPrice($this->newPriceLogic($productChoice['old-price'], $newDiscountPercent));
                    $product->setImages($productChoice['images']);
                    $product->setOriginalDiscountPercent($productChoice['discount-percent']);
                    $product->setDiscountPercent($newDiscountPercent);
                    $product->setForDelete(false);
                    $this->entityManager->persist($product);
                    $newChoices++;
                }else{
                    $existingProductChoice = $this->getExistingProductChoice($productChoice['website-id']);
                    $existingProductChoice->setForDelete(false);
                    $this->entityManager->persist($existingProductChoice);
                    $existingChoices++;
                }
            }

        }
        $this->entityManager->flush();
        return [
            'newChoices' => $newChoices,
            'existingChoices' => $existingChoices,
            'exceptionLogs' => $exceptionLogs,
        ];
    }

    public function createProductOptions(): void
    {
        $productIds = $this->productChoiceRepository->findALlProductsWithChoices();
        $productsWithChoices = $this->productRepository->findAllProductsWithChoices($productIds);
        foreach ($productsWithChoices as $product){

            $choices = $product->getProductChoices()->unwrap()->toArray();
            $productOptions = '';
            $optionType = '';
            foreach ($choices as $choice){
                $optionType = $choice->getOptionType();
                $productOptions.= '|'.$choice->getOptionValue();

            }

            $product->setOptionTypes($optionType);
            $product->setOptions($productOptions);
            $this->entityManager->persist($product);
        }
        $this->entityManager->flush();

    }
}

//{'is-product-choice': True,
//    'parent-website-id': 'emag-Код на продукта: 7290102992027',
//    'website-id': 'emag-Код на продукта: 7290102992232',
//    'option-type': 'Избери Парфюм:',
//    'option': 'Fresh',
//    'title': 'Омекотител Sano Fresh Bloom, Ултра концентрат, 40 изпирания, 1 л',
//    'old-price': 8.0,
//    'new-price': 7.0,
//    'images': '|https://s13emagst.akamaized.net/products/37764/37763748/images/res_0b380fa811c6a87df8cfe83d47681047.jpg',
//    'discount-percent': 11.0
//}
