<?php

namespace App\Service;

use App\Entity\Cart;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;

use App\Repository\MainCategoryRepository;
use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;
use YoHang88\LetterAvatar\LetterAvatar;


class GlobalVariables
{
    private CategoryRepository $categoryRepository;
    private Security $security;
    private ContainerInterface $container;
    private MainCategoryRepository $mainCategoryRepository;
    private BannerRepository $bannerRepository;


    public function __construct(CategoryRepository $categoryRepository, Security $security, ContainerInterface $container, MainCategoryRepository $mainCategoryRepository, BannerRepository $bannerRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
        $this->container = $container;
        $this->mainCategoryRepository = $mainCategoryRepository;
        $this->bannerRepository = $bannerRepository;
    }

    public function getCategoriesWithoutMainCategory(){
        return $this->categoryRepository->getCategoriesWithoutMainCategory();
    }

    public function getCategories()
    {
        return $this->categoryRepository->getCategoriesWithSubcategories();
    }

    public function getMainCategories()
    {
//        $mainCategories = $this->mainCategoryRepository->getAllCategoriesLevels();
//        dd($mainCategories);
        return $this->mainCategoryRepository->getAllCategoriesLevels();
    }

    public function getCart()
    {
        $request = $this->container->get('request_stack');
        $session = $request->getSession();
        $user = $this->security->getUser();
        if($user){
            $cart = $user->getCart();
        }else if ($session->get('cart')){
            $serializedCart = $session->get('cart');
            $cart = unserialize($serializedCart);
        } else{
            $cart = new Cart();
            $serializedCart = serialize($cart);
            $session->set('cart', $serializedCart);
        }

        return $cart;
    }

    public function getTotalCartItems()
    {
        $user = $this->security->getUser();
        if($user){
            $cart = $user->getCart();
        }else{
            $request = $this->container->get('request_stack');
            $session = $request->getSession();
            $serializedCart = $session->get('cart');
            $cart = unserialize($serializedCart);
        }

        if(!$cart){
            return 0;
        }
        $totalQuantity = 0;
        foreach ($cart->getProductOrders() as $productOrder){
            $totalQuantity+=$productOrder->getQuantity();
        }
        return $totalQuantity;
    }

    public function getTotalFavouriteItems(): ?int
    {
        $user = $this->security->getUser();
        if($user){
            $favourite = $user->getFavourite();
        }else{
            $request = $this->container->get('request_stack');
            $session = $request->getSession();
            $serializedFavourite = $session->get('favourite');
            $favourite = unserialize($serializedFavourite);
        }

        if(!$favourite){
            return 0;
        }

        return count($favourite->getFavouriteOrders());
    }

    public function getBanners(){
        return $this->bannerRepository->findAll();
    }

    public function getSearchQuery()
    {
        $request =  $this->container->get('request_stack')->getCurrentRequest();
        return $request->get('q');
    }

    public function getGlobalUser(): UserInterface
    {
        return $this->security->getUser();
    }

    public function getUserAvatar(): LetterAvatar
    {
        return new LetterAvatar($this->security->getUser()->getFullName());
    }

}