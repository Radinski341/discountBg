<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductOrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\GlobalVariables;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartEndpointController extends AbstractController
{
    #[Route(path: '/api/add-to-cart', name:'app_api_add_to_cart', methods: 'POST')]
    public function addToCart(
        Request $request,
        ProductRepository $productRepository,
        ProductChoiceRepository $productChoiceRepository,
        CartRepository $cartRepository,
        EntityManagerInterface $entityManager,
        ProductOrderRepository $productOrderRepository,
        CartService $cartService
    ): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $productSlug = $jsonData['slug'];
        $productChoice = $jsonData['productChoice'];
        $quantity = $jsonData['quantity'];

        $product = $productRepository->findOneBy(['slug' => $productSlug]);
        $choice = $productChoiceRepository->findOneBy(['product' => $product, 'optionValue' => $productChoice]);
        $user = $this->getUser();
        $session = $request->getSession();
        if($user){
            $cart = $cartRepository->findOneBy(['user' => $user]);
            if(!$cart){
                $cart = new Cart();
                $cart->setUser($user);
                $entityManager->persist($cart);
            }

            $productInCart = false;
            if(count($cart->getProductOrders()) > 0){
               $productOrder = $cartService->checkIfProductInCart($cart, $product, $choice, $quantity);
               if($productOrder){
                   $productInCart = true;
                   $entityManager->persist($productOrder);
               }
            }
            if(!$productInCart){
               $productOrder = $cartService->createProductOrder($cart, $product, $choice, $quantity);
               $entityManager->persist($productOrder);
            }
            $entityManager->flush();
            $entityManager->refresh($cart);
        }
        elseif(!$session->get('cart')) {
            $cart = new Cart();
            $productOrder = $cartService->createProductOrder($cart, $product, $choice, $quantity);
            $cart->addProductOrder($productOrder);
            $cartService->setSessionCart($cart);
        }else{
            $cart = $cartService->getSessionCart();
            $productInCart = false;
            if(count($cart->getProductOrders()) > 0){
                foreach ($cart->getProductOrders() as $productOrder){

                    if($productOrder->getProduct()->getId() === $product->getId() && $productOrder->getProductChoice()?->getId() === $choice?->getId()){
                        $productOrder->setQuantity($productOrder->getQuantity() + $quantity);
                        $cartService->setSessionCart($cart);
                        $productInCart = true;
                    }
                }
            }
            if(!$productInCart){
                $productOrder = $cartService->createProductOrder($cart, $product, $choice, $quantity);
                $cart->addProductOrder($productOrder);
                $cartService->setSessionCart($cart);
            }
        }

        $entityManager->flush();
        dump($cart->getProductOrders());
        return $this->render('public/partials/_cart-preview.html.twig', [
            'cart' => $cart
        ]);
    }

    #[Route(path: '/api/remove-from-cart', name:'app_api_remove_from_cart', methods: 'POST')]
    public function removeFromCart(Request $request, EntityManagerInterface $entityManager, CartService $cartService, ProductOrderRepository $productOrderRepository): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $orderId = $jsonData['orderId'];
        $productId = $jsonData['productId'];
        $productChoiceId = $jsonData['productChoiceId'];

        $user = $this->getUser();
        $cart = $cartService->findCart($user);

        if($user){
            $orderToRemove = $productOrderRepository->findOneBy(['id' => $orderId]);
            $entityManager->remove($orderToRemove);
            $entityManager->flush();
        }else{
            foreach ($cart->getProductOrders() as $productOrder){
                if($productOrder->getProduct()->getId() === intval($productId) && $productOrder->getProductChoice()?->getId() == $productChoiceId){
                    $cart->removeProductOrder($productOrder);
                    $cartService->setSessionCart($cart);
                    break;
                }
            }
        }

        $products = $cartService->findCartProducts($cart);
        $totalQuantity = $cartService->cartTotalQuantity($cart);
        $prices = $cartService->cartTotalPrice($cart);

        $totalPrice = $prices['totalNewPrice'];
        $totalOldPrice = $prices['totalOldPrice'];
        $totalDeliveryPrice = $prices['totalDeliveryPrice'];

        return $this->render('public/partials/_cart-info.html.twig', [
            'products' => $products,
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'totalOldPrice' => $totalOldPrice,
            'totalDeliveryPrice' => $totalDeliveryPrice,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'stripe_secret_key' => $this->getParameter('stripe_secret_key')
        ]);
    }

    #[Route(path: '/api/change-quantity', name:'app_api_change_quantity', methods: 'POST')]
    public function changeQuantity(Request $request, EntityManagerInterface $entityManager, ProductOrderRepository $productOrderRepository, ProductRepository $productRepository, ProductChoiceRepository $productChoiceRepository, CartService $cartService, GlobalVariables $globalVariables): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $orderId = $jsonData['orderId'];
        $changeDirection = $jsonData['changeDirection'];
        $productId = $jsonData['productId'];
        $productChoiceId = $jsonData['productChoiceId'];

        $user = $this->getUser();
        $cart = $cartService->findCart($user);

        if($user){
            $order = $productOrderRepository->findOneBy(['id' => $orderId]);
            if($changeDirection === 'up'){
                $order->setQuantity($order->getQuantity() + 1);
                $entityManager->persist($order);
            }else{
                if($order->getQuantity() === 1){
                    $entityManager->remove($order);
                }else{
                    $order->setQuantity($order->getQuantity() - 1);
                    $entityManager->persist($order);
                }
            }
            $entityManager->flush();
        }else{
            $quantityChange = 0;
            if($changeDirection === 'up'){
                $quantityChange = 1;
            }else{
                $quantityChange = -1;
            }
            $newSessionCart = false;
            foreach ($cart->getProductOrders() as $productOrder){
                if($productOrder->getProduct()->getId() === intval($productId) && $productOrder->getProductChoice()?->getId() == $productChoiceId){
                    if($changeDirection === 'down' && $productOrder->getQuantity() === 1){
                        $cart->removeProductOrder($productOrder);
                        $newSessionCart = true;
                        break;
                    }
                    $productOrder->setQuantity($productOrder->getQuantity() + $quantityChange);
                    $newSessionCart = true;
                    break;
                }
            }
            if($newSessionCart) $cartService->setSessionCart($cart);
        }

        $products = $cartService->findCartProducts($cart);
        $totalQuantity = $cartService->cartTotalQuantity($cart);
        $prices = $cartService->cartTotalPrice($cart);

        $totalPrice = $prices['totalNewPrice'];
        $totalOldPrice = $prices['totalOldPrice'];
        $totalDeliveryPrice = $prices['totalDeliveryPrice'];

        $globalVariables->getTotalCartItems();
        return $this->render('public/partials/_cart-info.html.twig', [
            'products' => $products,
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'totalOldPrice' => $totalOldPrice,
            'totalDeliveryPrice' => $totalDeliveryPrice,
            'currentProfilePage' => 'cart',
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'stripe_secret_key' => $this->getParameter('stripe_secret_key')
        ]);
    }
}