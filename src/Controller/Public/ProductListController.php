<?php

namespace App\Controller\Public;

use App\Entity\Favourite;
use App\Entity\Product;
use App\Form\Public\FilterFormType;
use App\Form\Public\ProductInfoFormType;
use App\Repository\CategoryRepository;
use App\Repository\FavouriteRepository;
use App\Repository\MainCategoryRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Service\FavouriteService;
use App\Service\SideBarHelper;
use App\Service\Translator;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductListController extends AbstractController
{
    #[Route('/products/all', 'app_public_all_products')]
    public function allProducts(
        ProductRepository $productRepository,
        Request $request,
        SideBarHelper $sideBarHelper,
        FavouriteRepository $favouriteRepository,
        FavouriteService $favouriteService,
        Translator $translator): Response
    {

        $filterForm = $this->createForm(FilterFormType::class, [
            'productsPerPage' => intval($request->get('productsPerPage')) === 0 ? 24 : intval($request->get('productsPerPage')),
            'order' => $request->get('order'),
            'priceRangeFrom' => $request->get('priceRangeFrom'),
            'priceRangeTo' => $request->get('priceRangeTo'),
            'q' => $request->get('q')
        ]);

        if($searchQuery = $request->get('q')){

            $queryBuilder = $productRepository->createFindProductsBySearchQuery($searchQuery, $translator->transliterate(textlat: $searchQuery));
        }else{
            $queryBuilder = $productRepository->createFindAllActiveProductsQueryBuilder(null, null, $request);
        }

        $adapter = new QueryAdapter($queryBuilder);
        $currentPage = $request->get('page') ? $request->get('page') : 1;
        $pagerfanta= Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $currentPage,
            $filterForm->getData()['productsPerPage']
        );

        $user = $this->getUser();
        if($user){
            $favouriteProducts = $favouriteRepository->getFavouriteOrderProductsId();
        }else{
            if(!$favourite = $favouriteService->getSessionFavourite()){
                $favourite = new Favourite();
                $favouriteService->setSessionFavourite($favourite);
            }

            $favouriteProducts = $favouriteService->getFavouriteOrderProductsId($favourite);
        }

        $this->checkIfProductIsFavourite($pagerfanta, $favouriteProducts);

        $formRender = $sideBarHelper->handleFilterRender($filterForm, $request);
        $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $urlParameters = $sideBarHelper->getUrlParameters($filterForm);
            return $this->redirectToRoute('app_public_all_products', $urlParameters);
        }

        return $this->render('public/products-list.html.twig', [
            'products' => $pagerfanta,
            'filterForm' => $formRender['filterFormView'],
            'maxPrice' => $formRender['maxPrice'],
            'urlToController' => $request->getPathInfo(),
            'currCategory' => null,
            'currSubCategory' => null,
            'mainCategory' => 'all',
            'searchQuery' => $request->get('q')
        ]);
    }

    #[Route('main-products/{mainCategorySlug}', 'app_public_main_category_products')]
    public  function mainCategoryProducts(
        String $mainCategorySlug,
        Request $request,
        MainCategoryRepository $mainCategoryRepository,
        ProductRepository $productRepository,
        FavouriteRepository $favouriteRepository,
        FavouriteService $favouriteService,
        SideBarHelper $sideBarHelper,
    )
    {
        $filterForm = $this->createForm(FilterFormType::class, [
            'productsPerPage' => intval($request->get('productsPerPage')) === 0 ? 24 : intval($request->get('productsPerPage')),
            'order' => $request->get('order'),
            'priceRangeFrom' => $request->get('priceRangeFrom'),
            'priceRangeTo' => $request->get('priceRangeTo'),
            'q' => $request->get('q')
        ]);

        $mainCategory = $mainCategoryRepository->findOneBy(['slug' => $mainCategorySlug]);
        $queryBuilder = $productRepository->createFindAllActiveProductsQueryBuilder(null, null, $request, $mainCategory);
        $adapter = new QueryAdapter($queryBuilder);
        $currentPage = $request->get('page') ? $request->get('page') : 1;
        $pagerfanta= Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $currentPage,
            $filterForm->getData()['productsPerPage']
        );

        $user = $this->getUser();
        if($user){
            $favouriteProducts = $favouriteRepository->getFavouriteOrderProductsId();
        }else{
            if(!$favourite = $favouriteService->getSessionFavourite()){
                $favourite = new Favourite();
                $favouriteService->setSessionFavourite($favourite);
            }

            $favouriteProducts = $favouriteService->getFavouriteOrderProductsId($favourite);
        }
        $this->checkIfProductIsFavourite($pagerfanta, $favouriteProducts);

        $formRender = $sideBarHelper->handleFilterRender($filterForm, $request);
        $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $urlParameters = $sideBarHelper->getUrlParameters($filterForm, null, null, $mainCategory);
            return $this->redirectToRoute('app_public_main_category_products', $urlParameters);
        }

        return $this->render('public/products-list.html.twig', [
            'products' => $pagerfanta,
            'filterForm' => $formRender['filterFormView'],
            'maxPrice' => $formRender['maxPrice'],
            'urlToController' => $request->getPathInfo(),
            'currCategory' => null,
            'currSubCategory' => null,
            'mainCategory' => $mainCategory,
            'pageTitle' => 'Всички продукити',
            'searchQuery' => $request->get('q')
        ]);
    }
    #[Route('/products/{categorySlug}', 'app_public_category_products')]
    public function productsInCategory(
        string $categorySlug,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        FavouriteRepository $favouriteRepository,
        FavouriteService $favouriteService,
        Request $request,
        SideBarHelper$sideBarHelper): Response
    {
        $filterForm = $this->createForm(FilterFormType::class, [
            'productsPerPage' => intval($request->get('productsPerPage')) === 0 ? 24 : intval($request->get('productsPerPage')),
            'order' => $request->get('order'),
            'priceRangeFrom' => $request->get('priceRangeFrom'),
            'priceRangeTo' => $request->get('priceRangeTo'),
            'q' => $request->get('q')
        ]);

        $category = $categoryRepository->findOneBy(['slug' => $categorySlug]);
        $mainCategory = $category->getMainCategory();
        $queryBuilder = $productRepository->createFindAllActiveProductsQueryBuilder($category, null, $request);
        $adapter = new QueryAdapter($queryBuilder);
        $currentPage = $request->get('page') ? $request->get('page') : 1;
        $pagerfanta= Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $currentPage,
            $filterForm->getData()['productsPerPage']
        );

        $user = $this->getUser();
        if($user){
            $favouriteProducts = $favouriteRepository->getFavouriteOrderProductsId();
        }else{
            if(!$favourite = $favouriteService->getSessionFavourite()){
                $favourite = new Favourite();
                $favouriteService->setSessionFavourite($favourite);
            }

            $favouriteProducts = $favouriteService->getFavouriteOrderProductsId($favourite);
        }
        $this->checkIfProductIsFavourite($pagerfanta, $favouriteProducts);

        $formRender = $sideBarHelper->handleFilterRender($filterForm, $request, $category);
        $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $urlParameters = $sideBarHelper->getUrlParameters($filterForm, $category);
            return $this->redirectToRoute('app_public_category_products', $urlParameters);
        }

        return $this->render('public/products-list.html.twig', [
            'products' => $pagerfanta,
            'filterForm' => $formRender['filterFormView'],
            'maxPrice' => $formRender['maxPrice'],
            'urlToController' => $request->getPathInfo(),
            'currCategory' => $category,
            'currSubCategory' => null,
            'mainCategory' => $mainCategory,
            'pageTitle' => 'Всички продукити',
            'searchQuery' => $request->get('q')
        ]);
    }
    #[Route('/products/{categorySlug}/{subCategorySlug}', 'app_public_sub_category_products')]
    public function productsInSubCategory(
        string $categorySlug,
        string $subCategorySlug,
        SubCategoryRepository $subCategoryRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        FavouriteRepository $favouriteRepository,
        FavouriteService $favouriteService,
        Request $request,
        SideBarHelper $sideBarHelper
    ): Response
    {
        $filterForm = $this->createForm(FilterFormType::class, [
            'productsPerPage' => intval($request->get('productsPerPage')) === 0 ? 24 : intval($request->get('productsPerPage')),
            'order' => $request->get('order'),
            'priceRangeFrom' => $request->get('priceRangeFrom'),
            'priceRangeTo' => $request->get('priceRangeTo'),
            'q' => $request->get('q')
        ]);

        $category = $categoryRepository->findOneBy(['slug' => $categorySlug]);
        $mainCategory = $category->getMainCategory();
        $subCategory = $subCategoryRepository->findOneBy(['category' => $category, 'slug' => $subCategorySlug]);
        $queryBuilder = $productRepository->createFindAllActiveProductsQueryBuilder($category, $subCategory, $request);
        $adapter = new QueryAdapter($queryBuilder);
        $currentPage = $request->get('page') ? $request->get('page') : 1;
        $pagerfanta= Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $currentPage,
            $filterForm->getData()['productsPerPage']
        );

        $user = $this->getUser();
        if($user){
            $favouriteProducts = $favouriteRepository->getFavouriteOrderProductsId();
        }else{
            if(!$favourite = $favouriteService->getSessionFavourite()){
                $favourite = new Favourite();
                $favouriteService->setSessionFavourite($favourite);
            }

            $favouriteProducts = $favouriteService->getFavouriteOrderProductsId($favourite);
        }
        $this->checkIfProductIsFavourite($pagerfanta, $favouriteProducts);

        $formRender = $sideBarHelper->handleFilterRender($filterForm, $request, $category, $subCategory);
        $filterForm->handleRequest($request);
        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $urlParameters = $sideBarHelper->getUrlParameters($filterForm, $category, $subCategory);
            return $this->redirectToRoute('app_public_sub_category_products', $urlParameters);
        }

        return $this->render('public/products-list.html.twig', [
            'products' => $pagerfanta,
            'filterForm' => $formRender['filterFormView'],
            'maxPrice' => $formRender['maxPrice'],
            'urlToController' => $request->getPathInfo(),
            'currCategory' => $category,
            'currSubCategory' => $subCategory,
            'mainCategory' => $mainCategory,
            'pageTitle' => 'Всички продукити',
            'searchQuery' => $request->get('q')
        ]);
    }

    #[Route('/product/{productSlug}', 'app_public_product_info')]
    public function productInfo(
        string $productSlug,
        ProductRepository $productRepository,
        ProductChoiceRepository $productChoiceRepository,
        FavouriteRepository $favouriteRepository,
        FavouriteService $favouriteService,
        Request $request,
        SluggerInterface $slugger): Response
    {
        $product = $productRepository->findOneBy(['slug' => $productSlug]);
        if(!$product){
            return $this->render('public/product-info.html.twig', ['product' => null,]);
        }
        $optionType = "";
        $options = explode('|', $product->getOptions());
        unset($options[0]);

        $optionValuesArray = null;
        $valuesArray = [];
        foreach ($product->getProductChoices() as $productChoice){
            $option = $productChoice->getOptionValue();
            $optionType = $productChoice->getOptionType();
            $sluggedOption = $slugger->slug($productChoice->getOptionType());
            $valuesArray[$option] = $this->generateUrl('app_public_product_info', [
                $sluggedOption->toString() => $option,
                'productSlug' => $product->getSlug()
            ]);
        }

        if($optionType !== ""){
            $optionValuesArray[$optionType] = $valuesArray;
        }

        if($productKey = $request->query->getIterator()->key()){
            $product = $productChoiceRepository->findOneBy(['product' => $product, 'optionValue' => $request->get($productKey)]);
        }
        $images = explode('|', $product->getImages());
        unset($images[0]);

        $form = $this->createForm(ProductInfoFormType::class, [
            'optionsArray' => $optionValuesArray,
        ]);

        $formView = $form->createView();

        $user = $this->getUser();
        if($user){
            if($product instanceof Product){
                if(in_array($product->getId(), $favouriteRepository->getFavouriteOrderProductsId())){
                    $product->setIsFavourite(true);
                }
            }else{
                if(in_array($product->getId(), $favouriteRepository->getFavouriteOrderProductChoicesId())){
                    $product->setIsFavourite(true);
                }
            }

        }else{
            if(!$favourite = $favouriteService->getSessionFavourite()){
                $favourite = new Favourite();
                $favouriteService->setSessionFavourite($favourite);
            }
            if($product instanceof Product){
                if(in_array($product->getId(), $favouriteService->getFavouriteOrderProductsId($favourite))){
                    $product->setIsFavourite(true);
                }
            }else{
                if(in_array($product->getId(), $favouriteService->getFavouriteOrderProductChoicesId($favourite))){
                    $product->setIsFavourite(true);
                }
            }

        }

        return $this->render('public/product-info.html.twig', [
            'form' => $formView,
            'formChoicesExist' => array_key_exists('choices', $formView->children),
            'product' => $product,
            'images' => $images,
            'allQueries' => $request->query->all() != [] ? $request->query->all() : null,
        ]);
    }

    private function checkIfProductIsFavourite($products, $favouriteProducts): void
    {
        foreach ($products as $product){
            if(in_array($product->getId(), $favouriteProducts)){
                $product->setIsFavourite(true);
            }
        }
    }
}