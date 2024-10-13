<?php

namespace App\Controller\Public;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\FavouriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use YoHang88\LetterAvatar\LetterAvatar;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_public_profile')]
    public function profile(OrderRepository $orderRepository):Response
    {
        $user = $this->getUser();
        return $this->render('public/profile.html.twig', [
            'user' => $user,
            'avatar' => new LetterAvatar($user->getFullName()),
            'currentProfilePage' => 'home',
            'activeOrders' => $orderRepository->getPublicPendingOrders($user),
            'favouritesCount' => count($user->getFavourite()->getFavouriteOrders())
        ]);
    }

    #[Route(path:'/favourite', name: 'app_public_favourite')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function favourite(FavouriteService $favouriteService): Response
    {

        $user = $this->getUser();

        $favourite = $favouriteService->findFavourite($user);
        $favourite = $favouriteService->removeOrdersWithMissingProducts($favourite);
        $products = $favouriteService->findFavouriteProducts($favourite);

        return $this->render('/public/favourite.html.twig', [
            'products' => $products,
            'currentProfilePage' => 'favourites'
        ]);
    }
}