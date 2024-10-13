<?php

namespace App\Service;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class DataFinder
{

    public function __construct()
    {
    }

    public function findAllDataFiles(string $folderPath): array
    {
        $finder = new Finder();
        $files = $finder->files()->in($folderPath);
        $fileNames = [];
        foreach ($files as $file) {
            $fileNames[] = $file->getFilename();
        }

        return $fileNames;
    }

    public function findAllCategoryLevels(string $folderPath): array
    {
        $fileNames = $this->findAllDataFiles($folderPath);

        $package = new Package(new EmptyVersionStrategy());
        $allCategoryLevels = [];
        foreach ($fileNames as $fileName){
            $path = $package->getUrl($folderPath.$fileName);
            $jsonData = file_get_contents($path);
            $data = json_decode($jsonData, 1);

            forEach ($data as $product){
                if($product['is-product-choice']){
                    continue;
                }
                $continue = false;
                $categories = explode('|', $product['categories']);
                if(count($categories) > 2){
                    $category = $categories[1];
                    $subCategory = $categories[2];
                }else{
                    $category = $categories[1];
                    $subCategory = null;
                }
                foreach ($allCategoryLevels as $categoryLevel){
                    if($categoryLevel['category'] === $category && $categoryLevel['subCategory'] === $subCategory){
                        $continue = true;
                    }
                }
                if($continue) continue;
                $allCategoryLevels[] = [
                    'category' => $category,
                    'subCategory' => $subCategory
                ];
            }
        }

        return $allCategoryLevels;
    }

}



















