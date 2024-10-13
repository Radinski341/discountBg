<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use App\Service\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchEndpointController extends AbstractController
{
    #[Route(path: '/api/search-preview', name: 'app_api_search_preview', methods: 'POST')]
    public function searchPreview(Request $request, Translator $translator, ProductRepository $productRepository): Response
    {
        $jsonContent = $request->getContent();
        $jsonData = json_decode($jsonContent, true);

        $query = $jsonData['query'];
        $translatedQuery = $translator->transliterate(textlat: $query);

        $products = $productRepository->getSearchPreviewProducts($query, $translatedQuery);
        if($query === ''){
            $products = [];
        }
        return $this->render('/public/partials/_search-preview.html.twig', [
            'products' => $products
        ]);
    }
}