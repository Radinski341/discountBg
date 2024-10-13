<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\ProductChoice;
use App\Entity\ProductOrder;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class CartService
{
    private EntityManagerInterface $entityManager;
    private ContainerInterface $container;
    private ProductRepository $productRepository;
    private CartRepository $cartRepository;
    private SluggerInterface $slugger;
    private WebsiteRepository $websiteRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        SluggerInterface $slugger,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        WebsiteRepository $websiteRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->slugger = $slugger;
        $this->websiteRepository = $websiteRepository;
    }

    public function createProductOrder(Cart $cart, Product $product, ?ProductChoice $choice,int $quantity)
    {
        $productOrder = new ProductOrder();
        $productOrder->setCart($cart);
        $productOrder->setProduct($product);
        if($choice){
            $productOrder->setProductChoice($choice);
        }
        $productOrder->setQuantity($quantity);
        return $productOrder;
    }

    public function checkIfProductInCart($cart, Product $product, ?ProductChoice $choice, $quantity)
    {
        $updatedProductOrder = null;
        foreach ($cart->getProductOrders() as $productOrder){
            if($productOrder->getProduct() === $product && $productOrder->getProductChoice() === $choice){
                $productOrder->setQuantity($productOrder->getQuantity() + $quantity);
                $updatedProductOrder = $productOrder;
            }
        }
        return $updatedProductOrder;
    }


    public function setSessionCart($sessionCart)
    {
        $session = $this->getSession();
        $serializedCart = serialize($sessionCart);
        $session->set('cart', $serializedCart);
    }

    public function getSessionCart()
    {
        $session = $this->getSession();
        $serializedCart = $session->get('cart');
        return unserialize($serializedCart, ['allowed_classes' => true]);
    }

    private function getSession()
    {
        $request = $this->container->get('request_stack');
       return $request->getSession();
    }

    public function findCartProducts($cart): array
    {
        $orders = $cart->getProductOrders();
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
            $product['quantity'] = $order->getQuantity();
            $product['order-id'] = $order->getId();
            $images = explode('|', $product['images']);
            $product['image'] = $images[1];
            $product['product-choice-id'] = $order->getProductChoice()?->getId();
            $products[] = $product;
        }
        return $products;
    }

    public function cartTotalQuantity($cart): int
    {
        $orders = $cart->getProductOrders();
        $totalQuantity = 0;
        foreach ($orders as $order){
            $totalQuantity+=$order->getQuantity();
        }
        return $totalQuantity;
    }

    public function cartTotalPrice($cart): array
    {
        $orders = $cart->getProductOrders();
        $totalPrice = 0;
        $totalOldPrice = 0;
        $totalPrices = [];
        $specificWebsitePrices = [];
        foreach ($orders as $order){
            $currentPrice = 0;
            $currentOldPrice = 0;
            $website = $order->getProduct()->getWebsiteName();

            if($order->getProductChoice()){
                $currentPrice+=$order->getProductChoice()->getNewPrice() * $order->getQuantity();
                $currentOldPrice+=$order->getProductChoice()->getOldPrice() * $order->getQuantity();
            }else{
                $currentPrice+=$order->getProduct()->getNewPrice() * $order->getQuantity();
                $currentOldPrice+=$order->getProduct()->getOldPrice() * $order->getQuantity();
            }

            $totalPrice += $currentPrice;
            $totalOldPrice += $currentOldPrice;
            $deliveryPrice = $order->getProduct()->getDeliveryPrice();

            if(!in_array($website, $specificWebsitePrices)){
                $specificWebsitePrices[$website] = ['website' => $website, 'price' => $currentPrice, 'deliveryPrice' => $deliveryPrice];
            } else {
                if($specificWebsitePrices[$website]['deliveryPrice'] < $deliveryPrice){
                    $specificWebsitePrices[$website]['deliveryPrice'] = $deliveryPrice;
                }
                $specificWebsitePrices[$website]['price'] += $currentPrice;
            }
        }
        $totalPrices['totalNewPrice'] = $totalPrice;
        $totalPrices['totalOldPrice'] = $totalOldPrice;
        $totalPrices['totalDeliveryPrice'] = 0;

        if($totalPrices['totalNewPrice'] < 300){
            foreach ($specificWebsitePrices as $specificWebsitePrice){
                $websiteFreeDelivery = $this->websiteRepository->findOneBy(['websiteName' => $specificWebsitePrice['website']])->getFreeDeliveryOver();
                if($specificWebsitePrice['price'] > $websiteFreeDelivery){
                    $specificWebsitePrice['deliveryPrice'] = 0;
                }
                $totalPrices['totalDeliveryPrice'] += $specificWebsitePrice['deliveryPrice'];
            }
        }
        return $totalPrices;
    }

    public function findCart(?User $user)
    {
        if($user){
            $cart = $this->cartRepository->findOneBy(['user' => $user]);
            if(!$cart){
                $cart = new Cart();
                $cart->setUser($user);
                $this->entityManager->persist($cart);
                $this->entityManager->flush();
            }
        }else{
            if(!$this->getSessionCart()) {
                $sessionCart = new Cart();
                $this->setSessionCart($sessionCart);
            }
            $cart = $this->getSessionCart();
        }
        return $cart;
    }

    public function removeOrdersWithMissingProducts($cart): Cart
    {
        $allProducts = $this->productRepository->getAllActiveProductsId();
        foreach ($cart->getProductOrders() as $cartOrder){

            if(!in_array($cartOrder->getProduct()->getId(), $allProducts)){
                $cart->removeProductOrder($cartOrder);
            }
        }

        $this->setSessionCart($cart);
        return $cart;
    }
}