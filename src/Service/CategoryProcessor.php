<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;


class CategoryProcessor
{
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;
    private SubCategoryRepository $subCategoryRepository;
    private CategoryMapping $categoryMapping;


    public function __construct(
        CategoryRepository $categoryRepository,
        SubCategoryRepository $subCategoryRepository,
        EntityManagerInterface $entityManager,
        CategoryMapping $categoryMapping,
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->categoryMapping = $categoryMapping;
    }

    public function processCategories(array $dataRows): void
    {
        dump('Memory before file processing: ' . memory_get_usage());
        $newCategories = [];
        $subCategoriesToProcess = [];
        $newSubCategories = [];
        $seenKeys = [];
        $counter = 0;
        foreach ($dataRows as $row) {
            // Skip product choices
            if (isset($row['is-product-choice']) && $row['is-product-choice']) {
                continue;
            }

            list($categoryTitle, $subCategoryTitle) = $this->parseCategoryField($row['categories']);

            // Get base category title from the mapping
            $baseCategoryTitle = $this->categoryMapping->getBaseCategory($row['website'], $categoryTitle) ?? $categoryTitle;

            if (!isset($newCategories[$baseCategoryTitle])) {
                $category = $this->findOrCreateCategory($baseCategoryTitle);
                $newCategories[$baseCategoryTitle] = $category;
            } else {
                $category = $newCategories[$baseCategoryTitle];
            }

            if ($subCategoryTitle) {
                $key = $category->getId() . ':' . $subCategoryTitle . ':' . $row['website']; // Create a unique key
                if(!isset($seenKeys[$key])) {
                    $subCategoriesToProcess[] = [
                        'category' => $category,
                        'subCategoryTitle' => $subCategoryTitle,
                        'website' => $row['website']
                    ];
                    $seenKeys[$key] = true;
                }
            }
        }

        // Flush categories first
        $this->entityManager->flush();
        unset($newCategories);
        unset($seenKeys);
        // Process and flush subcategories
        dump($subCategoriesToProcess);
        foreach ($subCategoriesToProcess as $item) {
            $categoryTitle = $item['category']->getTitle();
            $subCategoryTitle = $item['subCategoryTitle'];
            $baseSubcategoryTitle = $this->categoryMapping->getBaseSubcategory($item['website'], $categoryTitle, $subCategoryTitle) ?? $subCategoryTitle;

            if (!isset($newSubCategories[$categoryTitle])) {
                $newSubCategories[$categoryTitle] = [];
            }

            if (!in_array($baseSubcategoryTitle, $newSubCategories[$categoryTitle])) {
                $this->findOrCreateSubCategory($item['category'], $baseSubcategoryTitle);

                $newSubCategories[$categoryTitle][] = $baseSubcategoryTitle;
            }
        }

        $this->entityManager->flush();

        //Clear memory

        unset($subCategoriesToProcess);
        unset($newSubCategories);

        gc_collect_cycles();
        dump('Memory after file processing: ' . memory_get_peak_usage());
    }

    private function parseCategoryField(string $categoryField): array
    {
        $parts = explode('|', $categoryField);
        $category = trim($parts[1] ?? '');
        $subCategory = trim($parts[2] ?? null);
        return [$category, $subCategory];
    }

    private function findOrCreateCategory(string $title): Category
    {
        $category = $this->categoryRepository->findOneBy(['title' => $title]);
        dump($category);
        if (!$category) {
            dump('Category not found and creating it');
            $category = new Category();
            $category->setTitle($title);
            $category->setForDelete(false);
            $category->setIsActive(true);
            $category->setCreatedAt(new \DateTime('now'));
            $category->setUpdatedAt(new \DateTime('now'));
            $this->entityManager->persist($category);
        }

        return $category;
    }

    private function findOrCreateSubCategory(Category $category, string $title): void
    {
        $subCategory = $this->subCategoryRepository->findOneBy(['title' => $title, 'category' => $category]);
        if (!$subCategory) {
            $subCategory = new SubCategory();
            $subCategory->setTitle($title);
            $subCategory->setCategory($category);
            $subCategory->setForDelete(false);
            $subCategory->setIsActive(true);
            $subCategory->setCreatedAt(new \DateTime('now'));
            $subCategory->setUpdatedAt(new \DateTime('now'));
            $this->entityManager->persist($subCategory);
        }
    }


//    public function ifCategoryAndSubcategoryExist($allCategoryLevelsFromDatabase, $categoryLevelsFiles): bool
//    {
//        foreach ($allCategoryLevelsFromDatabase as $categoryLevelsDatabase) {
//            if ($categoryLevelsFiles['category'] === $categoryLevelsDatabase['category'] && $categoryLevelsFiles['subCategory'] === $categoryLevelsDatabase['subCategory']) {
//                $category = $this->categoryRepository->findOneBy(['title' => $categoryLevelsDatabase['category']]);
//                $category->setForDelete(false);
//                $subCategory = $this->subCategoryRepository->findOneBy(['title' => $categoryLevelsDatabase['subCategory'], 'category' => $category]);
//                $subCategory->setForDelete(false);
//                $this->entityManager->persist($category);
//                $this->entityManager->persist($subCategory);
//                return true;
//            }
//
//        }
//        return false;
//    }
//
//    public function ifCategoryExistAndSubcategoryDont($allCategoryLevelsFromDatabase, $categoryLevelsFiles): bool
//    {
//        foreach ($allCategoryLevelsFromDatabase as $categoryLevelsDatabase) {
//            if ($categoryLevelsFiles['category'] === $categoryLevelsDatabase['category'] && $categoryLevelsFiles['subCategory'] !== $categoryLevelsDatabase['subCategory']) {
//                return true;
//            }
//        }
//        return false;
//    }
//
//    public function createNewSubcategory($categoryExist, ?Category $newCategory, $categoryTitle, $subCategoryTitle): void
//    {
//        $newSubcategory = new SubCategory();
//        $newSubcategory->setIsActive(true);
//        $newSubcategory->setForDelete(false);
//        if(!$categoryExist){
//            $newSubcategory->setCategory($newCategory);
//            $newCategory->addSubCategory($newSubcategory);
//            $this->entityManager->persist($newCategory);
//        }else{
//            $category = $this->categoryRepository->findOneBy(['title' => $categoryTitle]);
//            if(!$category){
//                foreach ($this->entityManager->getUnitOfWork()->getScheduledEntityInsertions() as $categoryFromFiles){
//                    if($categoryFromFiles->getTitle() === $categoryTitle && $categoryFromFiles instanceof Category){
//                        $category = $categoryFromFiles;
//                    }
//                }
//            }
//            $newSubcategory->setCategory($category);
//            $category->addSubCategory($newSubcategory);
//            $category->setForDelete(false);
//            $this->entityManager->persist($category);
//
//        }
//        $newSubcategory->setTitle($subCategoryTitle);
//        $newSubcategory->setCreatedAt(new \DateTime());
//        $newSubcategory->setUpdatedAt(new \DateTime());
//
//        $this->entityManager->persist($newSubcategory);
//    }
//
//    public function createNewCategory($title): Category
//    {
//        $newCategory = new Category();
//        $newCategory->setTitle($title);
//        $newCategory->setForDelete(false);
//        $newCategory->setIsActive(true);
//        $newCategory->setCreatedAt(new \DateTime());
//        $newCategory->setUpdatedAt(new \DateTime());
//        $this->entityManager->persist($newCategory);
//        return $newCategory;
//    }
}