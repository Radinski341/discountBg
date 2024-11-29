<?php

namespace App\Controller\Public;

use App\Entity\Order;
use App\Entity\OrderTransaction;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\OrderPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    #[Route(path: '/pending-orders', name: 'app_public_pending_orders')]
    public function pendingOrders(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->getPublicPendingOrders($user);
        return $this->render('public/orders.html.twig', [
            'orders' => $orders,
            'currentProfilePage' => 'activeOrders'
        ]);
    }

    #[Route(path: '/closed-orders', name: 'app_public_closed_orders')]
    public function closedOrders(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->getPublicClosedOrders($user);
        return $this->render('public/orders.html.twig', [
            'orders' => $orders,
            'currentProfilePage' => 'ordersHistory'
        ]);
    }

    #[Route(path: '/order-info/{orderId}', name: 'app_public_order_info')]
    public function orderInfo(int $orderId, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneBy(['user' => $this->getUser(), 'id' => $orderId]);
        $returnedMoney = 0;
        foreach ($order->getOrderTransactions() as $orderTransaction) {
            if($orderTransaction->getStatus() === OrderTransaction::STATUS_RETURNED){
                $returnedMoney += $orderTransaction->getPrice() * $orderTransaction->getQuantity();
            }
        }

        return $this->render('/public/order-info.html.twig', [
            'order' => $order,
            'statusForReturn' => OrderTransaction::STATUS_FOR_RETURN,
            'statusReturned' => OrderTransaction::STATUS_RETURNED,
            'returnedMoney' => $returnedMoney,
        ]);
    }

    #[Route(path: '/pass-order', name: 'app_public_pass_order', methods: ['POST'])]
    public function passOrder(CartRepository $cartRepository, EntityManagerInterface $entityManager, Request $request, CartService $cartService, OrderPublisher $orderPublisher): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not logged in'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $paymentMethodId = $data['payment_method_id'];

        $cart = $cartRepository->findOneBy(['user' => $user]);
        $prices = $cartService->cartTotalPrice($cart);

        $totalPrice = $prices['totalNewPrice'];
        $totalDeliveryPrice = $prices['totalDeliveryPrice'];

        // Stripe payment process
        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        try {
            $intent = PaymentIntent::create([
                'amount' => ($totalPrice + $totalDeliveryPrice) * 100, // Amount in cents
                'currency' => 'bgn',
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
            ]);

            if ($intent->status == 'requires_action' && $intent->next_action->type == 'use_stripe_sdk') {
                return new JsonResponse(['client_secret' => $intent->client_secret]);
            }

            if ($intent->status == 'succeeded') {
                $order = new Order();
                $order->setUser($user);
                foreach ($cart->getProductOrders() as $productOrder) {
                    $orderTransaction = new OrderTransaction();
                    $orderTransaction->setProduct($productOrder->getProduct());
                    $orderTransaction->setQuantity($productOrder->getQuantity());
                    if ($productOrder->getProductChoice()) {
                        $orderTransaction->setProductChoice($productOrder->getProductChoice());
                        $orderTransaction->setPrice($productOrder->getProductChoice()->getNewPrice());
                        $orderTransaction->setOriginalWebsiteUrl($productOrder->getProductChoice()->getProductUrl());
                        $orderTransaction->setProductImage($productOrder->getProductChoice()->getMainImage());
                        $orderTransaction->setProductTitle($productOrder->getProductChoice()->getTitle());
                    } else {
                        $orderTransaction->setPrice($productOrder->getProduct()->getNewPrice());
                        $orderTransaction->setOriginalWebsiteUrl($productOrder->getProduct()->getProductUrl());
                        $orderTransaction->setProductImage($productOrder->getProduct()->getMainImage());
                        $orderTransaction->setProductTitle($productOrder->getProduct()->getTitle());
                    }
                    $orderTransaction->setOrderParent($order);
                    $orderTransaction->setStatus(OrderTransaction::STATUS_PENDING);
                    $entityManager->persist($orderTransaction);
                    $entityManager->remove($productOrder);
                }
                $order->setTotalPrice($totalPrice);
                $order->setStatus(Order::STATUS_PENDING);

                $entityManager->persist($order);
                $entityManager->flush();

                $this->addFlash('success', 'Your payment was successful and your order has been placed.');
                $orderPublisher->publishNewPendingOrder($order->getId());
                return new JsonResponse([
                    'redirect' => $this->generateUrl('app_homepage'),
                    'client_secret' => $intent->client_secret
                ], Response::HTTP_OK);


            }

            $this->addFlash('error', 'Възникна проблем при поръчването. Моля проверете даните си и опитайте от ново.');

            return new JsonResponse(['error' => 'Payment failed'], Response::HTTP_OK);
        }catch (\Exception $exception){
            $this->addFlash('error', 'Възникна проблем при поръчването. Моля проверете даните си и опитайте от ново.');

            return new JsonResponse(['error' => 'Payment failed'], Response::HTTP_BAD_REQUEST);
        }
    }
}