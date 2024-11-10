<?php

namespace App\Controller\Api;

use App\Entity\OrderTransaction;
use App\Repository\OrderTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderInfoEndpointController extends AbstractController
{
    #[Route(path: '/admin/api/change-order-transaction-status', name: 'app_api_change_order_transaction_status', methods: 'POST')]
    public function changeOrderTransactionStatus(Request $request, OrderTransactionRepository $orderTransactionRepository, EntityManagerInterface $entityManager): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $transactionId = $jsonData['transactionId'];
        $status = $jsonData['status'];
        $statusDescription = $jsonData['statusDescription'];


        $transaction = $orderTransactionRepository->findOneBy(['id' => $transactionId]);
        $transaction->setStatus($status);
        if($statusDescription){
            $transaction->setStatusDescription($statusDescription);
        }else{
            switch ($status){
                case OrderTransaction::STATUS_ORDERED:
                    $transaction->setStatusDescription('The item is on it\'s way');
                    break;
                case OrderTransaction::STATUS_DELIVERED:
                    $transaction->setStatusDescription('The item is delivered successfully');
                    break;
                case OrderTransaction::STATUS_FOR_RETURN:
                    $transaction->setStatusDescription('There is problem with this item, it have to be returned');
                    break;
                case OrderTransaction::STATUS_RETURNED:
                    $transaction->setStatusDescription('The item is returned successfully');
                    break;
                default:
                    $transaction->setStatusDescription('Our team is working on this item');
            }
        }

        $entityManager->persist($transaction);
        $entityManager->flush();

        return new Response('Status changed');
    }

    #[Route(path: '/admin/api/get-status-description', name: 'app_api_get_status_description', methods: 'POST')]
    public function getStatusDescription(Request $request, OrderTransactionRepository $orderTransactionRepository): JsonResponse
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $transactionId = $jsonData['transactionId'];

        $transaction = $orderTransactionRepository->findOneBy(['id' => $transactionId]);

        if (!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        $transactionArray = [];
        $transactionArray['image'] = $transaction->getProductImage();
        $transactionArray['statusDescription'] = $transaction->getStatusDescription();
        $transactionArray['status'] = $transaction->getStatus();
        return new JsonResponse($transactionArray, 200);
    }
}