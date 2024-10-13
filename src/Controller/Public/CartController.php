<?php

namespace App\Controller\Public;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route(path:'/cart', name: 'app_public_cart')]
    public function cart(CartService $cartService): Response
    {
        $user = $this->getUser();
        $cart = $cartService->findCart($user);

        $cart = $cartService->removeOrdersWithMissingProducts($cart);
        $products = $cartService->findCartProducts($cart);
        $totalQuantity = $cartService->cartTotalQuantity($cart);
        $prices = $cartService->cartTotalPrice($cart);

        $totalPrice = $prices['totalNewPrice'];
        $totalOldPrice = $prices['totalOldPrice'];
        $totalDeliveryPrice = $prices['totalDeliveryPrice'];
        return $this->render('/public/cart.html.twig', [
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