<?php

namespace App\Service;

use App\Repository\BaseCategoryRepository;
use App\Repository\BaseSubcategoryRepository;

class CategoryMapping
{
    private BaseCategoryRepository $baseCategoryRepository;
    private BaseSubcategoryRepository $baseSubcategoryRepository;

    public function __construct(BaseCategoryRepository $baseCategoryRepository, BaseSubcategoryRepository $baseSubcategoryRepository)
    {
        $this->baseCategoryRepository = $baseCategoryRepository;
        $this->baseSubcategoryRepository = $baseSubcategoryRepository;
    }

    public function getBaseCategory(string $website, string $categoryTitle): ?string
    {
        $baseCategory = $this->baseCategoryRepository->findOneBy([
            'website' => $website,
            'replaceCategory' => $categoryTitle,
        ]);

        return $baseCategory?->getTitle();
    }

    public function getBaseSubcategory(string $website, string $categoryTitle, string $subcategoryTitle): ?string
    {
        $baseSubcategory = $this->baseSubcategoryRepository->findOneBy([
            'website' => $website,
            'category' => $categoryTitle,
            'replaceSubcategory' => $subcategoryTitle,
        ]);

        return $baseSubcategory?->getTitle();
    }
}