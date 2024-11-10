<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\OrderTransaction;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderEndpointController extends AbstractController
{
    #[Route(path: '/admin/api/change-order-status', name: 'app_api_change_order_status', methods: 'POST')]
    public function changeOrderStatus(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $orderId = $jsonData['orderId'];
        $status = $jsonData['status'];
        $statusDescription = $jsonData['statusDescription'];

        $order = $orderRepository->findOneBy(['id' => $orderId]);

        $errorMessage = null;
        switch ($status){
            case Order::STATUS_IN_PROGRESS:
                $order->setStatusDescription('Taken by '. $this->getUser()->getUserIdentifier());
                $order->setTakenBy($this->getUser());
                $order->setStatus($status);
                break;
            case Order::STATUS_RETAKEN:
                $order->setStatusDescription('Retaken by '. $this->getUser()->getUserIdentifier() . ' from '. $order->getTakenBy()->getUserIdentifier());
                $order->setTakenBy($this->getUser());
                $order->setStatus($status);
                break;
            case Order::STATUS_ORDERED:
                foreach ($order->getOrderTransactions() as $orderTransaction){
                    if(
                        $orderTransaction->getStatus() === OrderTransaction::STATUS_PENDING ||
                        $orderTransaction->getStatus() === OrderTransaction::STATUS_FOR_RETURN
                    ){
                        $errorMessage = 'All items in the order have to be ordered to set the status to ORDERED!!!';
                        break;
                    }
                }
                if(!$errorMessage){
                    $order->setStatusDescription('Items ordered, waiting to be delivered');
                    $order->setStatus($status);
                }
                break;
            case Order::STATUS_PROBLEM:
                $order->setStatusDescription('There is some problems with this order');
                $order->setStatus($status);
                break;
            case Order::STATUS_CLOSED:
                foreach ($order->getOrderTransactions() as $orderTransaction){
                    if(
                        $orderTransaction->getStatus() === OrderTransaction::STATUS_PENDING ||
                        $orderTransaction->getStatus() === OrderTransaction::STATUS_ORDERED ||
                        $orderTransaction->getStatus() === OrderTransaction::STATUS_FOR_RETURN
                    ){
                        $errorMessage = 'All items in the order have to be finished before closing the order!!!';
                        break;
                    }
                }
                if(!$errorMessage){
                    $order->setStatusDescription('Order successfully delivered!');
                    $order->setStatus($status);
                }
        }
        if($statusDescription){
            $order->setStatusDescription($statusDescription);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse([
            'status' => $order->getStatus(),
            'statusDescription' => $order->getStatusDescription(),
            'error' => $errorMessage
        ], 200);
    }

    #[Route(path: '/admin/api/get-order-status', name: 'app_api_get_order_status', methods: 'POST')]
    public function getOrderStatus(Request $request, OrderRepository $orderRepository): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $orderId = $jsonData['orderId'];

        $order = $orderRepository->findOneBy(['id' => $orderId]);
        return new Response('', 200 , [
            'status' => $order->getStatus(),
            'statusDescription' => $order->getStatusDescription()
        ]);
    }
}