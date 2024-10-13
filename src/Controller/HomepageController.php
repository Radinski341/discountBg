<?php

namespace App\Controller;

use App\Repository\CarouselRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/', 'app_homepage')]
    public function homepage(CarouselRepository $carouselRepository) :Response
    {

        $carousels = $carouselRepository->findAll();



        return $this->render('public/homepage.html.twig', [
            'carousels' => $carousels
        ]);
    }
}