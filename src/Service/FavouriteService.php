<?php

namespace App\Service;

use App\Entity\Favourite;
use App\Entity\FavouriteOrder;
use App\Entity\Product;
use App\Entity\ProductChoice;
use App\Entity\User;
use App\Repository\FavouriteRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FavouriteService
{
    private ContainerInterface $container;
    private FavouriteRepository $favouriteRepository;
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private SluggerInterface $slugger;

    public function __construct(
        ContainerInterface $container,
        FavouriteRepository $favouriteRepository,
        ProductRepository $productRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->favouriteRepository = $favouriteRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->slugger = $slugger;
    }

    private function getSession()
    {
        $request = $this->container->get('request_stack');
        return $request->getSession();
    }

    public function checkIfOrderInFavourite(Favourite $favourite, Product $product, ?ProductChoice $productChoice):bool
    {
        foreach ($favourite->getFavouriteOrders() as $favouriteOrder){
            if($favouriteOrder->getProduct()->getId() === $product->getId() && $favouriteOrder->getProductChoice()?->getId() === $productChoice?->getId()){
                return true;
            }
        }
        return false;
    }

    public function findFavouriteOrder(Favourite $favourite, Product $product, ?ProductChoice $productChoice): ?FavouriteOrder
    {
        foreach ($favourite->getFavouriteOrders() as $favouriteOrder){
            if($favouriteOrder->getProduct()->getId() === $product->getId() && $favouriteOrder->getProductChoice()?->getId() === $productChoice?->getId()){
                return $favouriteOrder;
            }
        }
        return null;
    }

    public function createFavouriteOrder(Product $product, ?ProductChoice $productChoice): FavouriteOrder
    {
        $favouriteOrder = new FavouriteOrder();
        $favouriteOrder->setProduct($product);
        if($productChoice){
            $favouriteOrder->setProductChoice($productChoice);
        }
        return $favouriteOrder;
    }

    public function setSessionFavourite($sessionFavourite): void
    {
        $session = $this->getSession();
        $serializedFavourite = serialize($sessionFavourite);
        $session->set('favourite', $serializedFavourite);
    }

    public function getSessionFavourite()
    {
        $session = $this->getSession();
        $serializedFavourite = $session->get('favourite');
        return unserialize($serializedFavourite, ['allowed_classes' => true]);
    }

    public function findFavourite(?User $user)
    {
        if($user){
            $favourite = $this->favouriteRepository->findOneBy(['user' => $user]);
            if(!$favourite){
                $favourite = new Favourite();
                $favourite->setUser($user);
                $this->entityManager->persist($favourite);
                $this->entityManager->flush();
            }
        }else{
            if(!$this->getSessionFavourite()) {
                $sessionFavourite = new Favourite();
                $this->setSessionFavourite($sessionFavourite);
            }
            $favourite = $this->getSessionFavourite();
        }
        return $favourite;
    }

    public function getFavouriteOrderProductsId(Favourite $favourite): array
    {
        $productsId = [];
        foreach ($favourite->getFavouriteOrders() as $favouriteOrder){
            $productsId[] = $favouriteOrder->getProduct()->getId();
        }
        return $productsId;
    }
    public function getFavouriteOrderProductChoicesId(Favourite $favourite): array
    {
        $productsId = [];
        foreach ($favourite->getFavouriteOrders() as $favouriteOrder){
            $productsId[] = $favouriteOrder->getProductChoice()?->getId();
        }
        return $productsId;
    }

    public function findFavouriteProducts($favourite): array
    {
        $orders = $favourite->getFavouriteOrders();
        $products = [];
        foreach ($orders as $order){
            if($order->getProductChoice()){
                $product = $this->productRepository->getProductWithChoice($order->getProduct()->getSlug(), $order->getProductChoice()->getOptionValue())[0];
                $sluggedOptionType = $this->slugger->slug($order->getProductChoice()->getOptionType());
                $product['queryParams'] = [
                    $sluggedOptionType->toString() => $order->getProductChoice()->getOptionValue(),
                    'productSlug' => $order->getProduct()->getSlug()
                ];

            }else{
                $product = $this->productRepository->getProductWithoutChoice($order->getProduct()->getSlug())[0];
                $product['queryParams'] = [
                    'productSlug' => $order->getProduct()->getSlug()
                ];
            }
            $product['order-id'] = $order->getId();
            $images = explode('|', $product['images']);
            $product['image'] = $images[1];
            $product['product-choice-id'] = $order->getProductChoice()?->getId();
            $products[] = $product;
        }
        return $products;
    }

    public function removeOrdersWithMissingProducts($favourite): Favourite
    {
        $allProducts = $this->productRepository->getAllActiveProductsId();
        foreach ($favourite->getFavouriteOrders() as $favouriteOrder){

            if(!in_array($favouriteOrder->getProduct()->getId(), $allProducts)){
                $favourite->removeFavouriteOrder($favouriteOrder);
            }
        }

        $this->setSessionFavourite($favourite);
        return $favourite;
    }
}