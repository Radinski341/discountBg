<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\MainCategory;
use App\Entity\SubCategory;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;

class SideBarHelper
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function handleFilterRender($filterForm, Request $request, ?Category $category = null, ?SubCategory $subCategory = null): array
    {
        $repositoryCriteria = [];
        if($category){
            $repositoryCriteria['category'] = $category;
            if($subCategory){
                $repositoryCriteria['subCategory'] = $subCategory;
            }
        }

        $maxPrice = $this->productRepository->findOneBy($repositoryCriteria, ['newPrice' => 'DESC'])?->getNewPrice();
        $filterFormView = $filterForm->createView();
        $filterFormView['priceRangeFrom']->vars['id'] = 'fromSlider';
        $filterFormView['priceRangeFrom']->vars['value'] = $request->get('priceRangeFrom') ? intval($request->get('priceRangeFrom')) : 0;
        $filterFormView['priceRangeTo']->vars['id'] = 'toSlider';
        $filterFormView['priceRangeTo']->vars['value'] = $request->get('priceRangeTo') ? intval($request->get('priceRangeTo')) : $maxPrice;


        return [
            'maxPrice' => $maxPrice,
            'filterFormView' => $filterFormView
        ];
    }

    public function getUrlParameters($filterForm, ?Category $category = null, ?SubCategory $subCategory= null, ?MainCategory $mainCategory = null): array
    {
        $urlParameters = [
            'productsPerPage' => $filterForm->getData()['productsPerPage'],
            'order' => $filterForm->getData()['order'],
            'priceRangeFrom' => $filterForm->getData()['priceRangeFrom'],
            'priceRangeTo' => $filterForm->getData()['priceRangeTo'],
            'q' => $filterForm->getData()['q']
        ];
        if($mainCategory){
            $urlParameters['mainCategorySlug'] = $mainCategory->getSlug();
        }

        if($category){
            $urlParameters['categorySlug'] = $category->getSlug();
            if($subCategory){
                $urlParameters['subCategorySlug'] = $subCategory->getSlug();
            }
        }
        return $urlParameters;
    }
}