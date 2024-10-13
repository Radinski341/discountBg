<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\Favourite;
use App\Entity\FavouriteOrder;
use App\Repository\CartRepository;
use App\Repository\FavouriteOrderRepository;
use App\Repository\FavouriteRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductOrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\FavouriteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavouriteEndpointController extends AbstractController
{
    #[Route(path: '/api/add-to-favourite', name:'app_api_add_to_favourite', methods: 'POST')]
    public function addToFavourite(
        Request $request,
        ProductRepository $productRepository,
        ProductChoiceRepository $productChoiceRepository,
        FavouriteRepository $favouriteRepository,
        EntityManagerInterface $entityManager,
        ProductOrderRepository $productOrderRepository,
        FavouriteService $favouriteService
    ): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $productSlug = $jsonData['slug'];
        $productChoice = $jsonData['productChoice'];

        $product = $productRepository->findOneBy(['slug' => $productSlug]);
        $choice = $productChoiceRepository->findOneBy(['product' => $product, 'optionValue' => $productChoice]);
        $user = $this->getUser();
        $session = $request->getSession();
        if($user){
            $favourite = $favouriteRepository->findOneBy(['user' => $user]);
            if(!$favourite){
                $favourite = new Favourite();
                $favourite->setUser($user);
                $entityManager->persist($favourite);
            }

            if(!$favouriteService->checkIfOrderInFavourite($favourite, $product, $choice)){
                $favouriteOrder = $favouriteService->createFavouriteOrder($product, $choice);
                $favouriteOrder->setFavourite($favourite);
                $entityManager->persist($favouriteOrder);
            }else{
                $favouriteOrder = $favouriteService->findFavouriteOrder($favourite, $product, $choice);
                $entityManager->remove($favouriteOrder);
            }
            $entityManager->flush();
        }
        elseif(!$session->get('favourite')) {
            $sessionFavourite = new Favourite();
            $favouriteOrder = $favouriteService->createFavouriteOrder($product, $choice);
            $sessionFavourite->addFavouriteOrder($favouriteOrder);
            $favouriteService->setSessionFavourite($sessionFavourite);

        }else{
            $sessionFavourite = $favouriteService->getSessionFavourite();
            if(!$favouriteService->checkIfOrderInFavourite($sessionFavourite, $product, $choice)){
                $favouriteOrder = $favouriteService->createFavouriteOrder($product, $choice);
                $sessionFavourite->addFavouriteOrder($favouriteOrder);

            }else{
                $favouriteOrder = $favouriteService->findFavouriteOrder($sessionFavourite, $product, $choice);
                $sessionFavourite->removeFavouriteOrder($favouriteOrder);
            }
            $favouriteService->setSessionFavourite($sessionFavourite);
        }

        return new Response('New item added in favourites');
    }

    #[Route(path: '/api/remove-from-favourite', name:'app_api_remove_from_favourite', methods: 'POST')]
    public function removeFromFavourite(FavouriteOrderRepository $favouriteOrderRepository, FavouriteService $favouriteService, EntityManagerInterface $entityManager, Request $request): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $orderId = $jsonData['orderId'];
        $productId = $jsonData['productId'];
        $productChoiceId = $jsonData['productChoiceId'];
        $user = $this->getUser();
        if($user){
            $favouriteOrder = $favouriteOrderRepository->findOneBy(['id' => $orderId]);
            $entityManager->remove($favouriteOrder);
            $entityManager->flush();
        }else{
            $favourite = $favouriteService->getSessionFavourite();
            foreach ($favourite->getFavouriteOrders() as $favouriteOrder){
                if($favouriteOrder->getProduct()->getId() === intval($productId) && $favouriteOrder->getProductChoice()?->getId() == $productChoiceId){
                    $favourite->removeFavouriteOrder($favouriteOrder);
                    $favouriteService->setSessionFavourite($favourite);
                    break;
                }
            }
        }


        return new Response('Item deleted');
    }
}