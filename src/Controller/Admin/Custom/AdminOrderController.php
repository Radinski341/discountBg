<?php

namespace App\Controller\Admin\Custom;

use App\Entity\Order;
use App\Entity\OrderTransaction;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

class AdminOrderController extends AbstractController
{
    #[Route(path: '/admin/pending-orders', name: 'app_admin_pending_orders')]
    public function pendingOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_PENDING]);
        $mercurePublicUrl = $_ENV['MERCURE_PUBLIC_URL']; // Fetch from environment variables

        return $this->render('/admin/orders-list.html.twig', [
            'orders' => $orders,
            'title' => 'Pending orders',
            'mercurePublicUrl' => $mercurePublicUrl, // Pass it to Twig
        ]);
    }

    #[Route(path: '/admin/in-process-orders', name: 'app_admin_in_process_orders')]
    public function inProcessOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_IN_PROGRESS]);
        $orders += $orderRepository->findBy(['status' => Order::STATUS_RETAKEN]);

        return $this->render('/admin/orders-list.html.twig', [
            'orders' => $orders,
            'title' => 'Orders in process'
        ]);
    }

    #[Route(path: '/admin/ordered-orders', name: 'app_admin_ordered_orders')]
    public function orderedOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_ORDERED]);

        return $this->render('/admin/orders-list.html.twig', [
            'orders' => $orders,
            'title' => 'Ordered orders'
        ]);
    }

    #[Route(path: '/admin/problem-orders', name: 'app_admin_problem_orders')]
    public function problemOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_PROBLEM]);

        return $this->render('/admin/orders-list.html.twig', [
            'orders' => $orders,
            'title' => 'Problem orders'
        ]);
    }
    #[Route(path: '/admin/closed-orders', name: 'app_admin_closed_orders')]
    public function closedOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => Order::STATUS_CLOSED]);

        return $this->render('/admin/orders-list.html.twig', [
            'orders' => $orders,
            'title' => 'Closed orders'
        ]);
    }

    #[Route(path: '/admin/order-info', name: 'app_admin_order_info')]
    public function orderInfo(OrderRepository $orderRepository, Request $request): Response
    {
        $orderId = $request->get('orderId');
        $order = $orderRepository->findOneBy(['id' => $orderId]);
        $orderedBy = $order->getUser();
        $user = $this->getUser();
        $statusChoices = [
            OrderTransaction::STATUS_PENDING,
            OrderTransaction::STATUS_ORDERED,
            OrderTransaction::STATUS_DELIVERED,
            OrderTransaction::STATUS_FOR_RETURN,
            OrderTransaction::STATUS_RETURNED
        ];

        $statusChoiceColors = [
            OrderTransaction::STATUS_PENDING=> 'yellow',
            OrderTransaction::STATUS_ORDERED => 'blue',
            OrderTransaction::STATUS_DELIVERED => 'green',
            OrderTransaction::STATUS_FOR_RETURN => 'red',
            OrderTransaction::STATUS_RETURNED => 'orange'
        ];

        $allowActions = false;
        if(!$order->getTakenBy()){
            $statusButtons = [
                Order::STATUS_IN_PROGRESS => ['text' => 'Taker order', 'type' => 'btn-warning', 'value' => Order::STATUS_IN_PROGRESS],
            ];
        }elseif ($order->getTakenBy()->getId() === $user->getId()){
            $statusButtons = [
                Order::STATUS_ORDERED => ['text' => 'Set ordered', 'type' => 'btn-primary', 'value' => Order::STATUS_ORDERED],
                Order::STATUS_PROBLEM => ['text' => 'Report problem', 'type' => 'btn-danger', 'value' => Order::STATUS_PROBLEM],
                Order::STATUS_CLOSED => ['text' => 'Close order', 'type' => 'btn-success', 'value' => Order::STATUS_CLOSED]
            ];
            $allowActions = true;
        }else{
            $statusButtons = [
                Order::STATUS_IN_PROGRESS => ['text' => 'Re take', 'type' => 'btn-warning', 'value' => Order::STATUS_RETAKEN],
            ];
        }


        return $this->render('/admin/order-info.html.twig', [
            'order' => $order,
            'orderTransactions' => $order->getOrderTransactions(),
            'statusChoices' => $statusChoices,
            'statusChoiceColors' => $statusChoiceColors,
            'statusButtons' => $statusButtons,
            'allowActions' => $allowActions,
            'orderedBy' => $orderedBy
        ]);
    }
}