<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\MainCategory;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\Translation\t;


/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private ProductChoiceRepository $productChoiceRepository;

    public function __construct(ManagerRegistry $registry, ProductChoiceRepository $productChoiceRepository)
    {
        parent::__construct($registry, Product::class);
        $this->productChoiceRepository = $productChoiceRepository;
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findAllProductsWebsiteId($website): array
    {
        $allProducts =  $this->createQueryBuilder('p')
            ->andWhere('p.websiteName = :website')
            ->setParameter(':website', $website)
            ->select(['p.websiteId'])
            ->getQuery()
            ->getResult()
        ;
        $allProductsArray = [];
        foreach ($allProducts as $product){
            $allProductsArray[] = $product['websiteId'];
        }
        return $allProductsArray;
    }

    public function createFindAllActiveProductsQueryBuilder(?Category $category, ?SubCategory $subCategory, Request $request, ?MainCategory $mainCategory = null): QueryBuilder
    {
        $queryBuilder =  $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true');

        if($mainCategory){
            $categories = $mainCategory->getCategories();

            if (!empty($categories)) {
                $queryBuilder->andWhere('p.category IN (:categories)')
                    ->setParameter('categories', $categories);
            }
        } elseif($category){
            $queryBuilder->andWhere('p.category = :category')
                ->setParameter(':category', $category);
        }
        if($subCategory){
            $queryBuilder->andWhere('p.subCategory = :subCategory')
                ->setParameter(':subCategory', $subCategory);
        }
        if($order = $request->get('order')){
            switch ($order){
                case 'discount':
                    $queryBuilder->orderBy('p.discountPercent', 'DESC');
                    break;
                case 'priceAsc':
                    $queryBuilder->orderBy('p.newPrice', 'ASC');
                    break;
                case 'priceDesc':
                    $queryBuilder->orderBy('p.newPrice', 'DESC');
                    break;
                default:
                   break;
            }
        }
        if($request->get('priceRangeFrom') || $request->get('priceRangeTo')){
            $queryBuilder
                ->andWhere('p.newPrice >= :priceRangeFrom')
                ->setParameter(':priceRangeFrom', $request->get('priceRangeFrom'))
                ->andWhere('p.newPrice <= :priceRangeTo')
                ->setParameter(':priceRangeTo', $request->get('priceRangeTo'));
        }

        return $queryBuilder;
    }

    public function findAllProductsWithChoices($productIds)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($productIds as $index => $productId) {
            if ($index === 0) {
                // The first condition uses "where" instead of "orWhere"
                $queryBuilder->where("p.id = :productId{$index}")
                    ->setParameter(":productId{$index}", $productId['id']);
            } else {
                // Subsequent conditions use "orWhere"
                $queryBuilder->orWhere("p.id = :productId{$index}")
                    ->setParameter(":productId{$index}", $productId['id']);
            }
        }

        return $queryBuilder->getQuery()->getResult();

    }

    public function getProductWithChoice($productSlug, $optionValue)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.productChoices', 'pc')
            ->where('p.slug = :productSlug')
            ->setParameter(':productSlug', $productSlug)
            ->andWhere('pc.optionValue = :optionValue')
            ->setParameter(':optionValue', $optionValue)
            ->select([
                'p.id',
                'p.slug',
                'pc.websiteId',
                'pc.productUrl',
                'pc.title',
                'pc.oldPrice',
                'pc.newPrice',
                'pc.images',
                'pc.discountPercent'
            ])
            ->getQuery()
            ->getResult();
    }

    public function getProductWithoutChoice($productSlug)
    {
        return  $this->createQueryBuilder('p')
            ->where('p.slug = :productSlug')
            ->setParameter(':productSlug', $productSlug)
            ->select([
                'p.id',
                'p.slug',
                'p.websiteId',
                'p.productUrl',
                'p.title',
                'p.oldPrice',
                'p.newPrice',
                'p.images',
                'p.discountPercent'
            ])
            ->getQuery()
            ->getResult();
    }

    public function refreshForDeleteField()
    {
        if(count($this->findAll()) > 0) {
            return $this->createQueryBuilder('p')
                ->update()
                ->set('p.forDelete', true)
                ->getQuery()
                ->execute();
        }
    }

    public function preventFromDelete()
    {
        if(count($this->findAll()) > 0){
            return $this->createQueryBuilder('p')
                ->update()
                ->set('p.forDelete', 'false')
                ->getQuery()
                ->execute();
        }

    }

    public function deleteALlMissingProducts($website): bool
    {
        $productsForDelete = $this->findBy(['forDelete' => true, 'websiteName' => $website]);
        if(count($productsForDelete) === 0){
            return false;
        }
        foreach ($productsForDelete as $product){
            foreach ($product->getProductOrders() as $productOrder){
                $this->_em->remove($productOrder);
            }
            foreach ($product->getOrderTransactions() as $orderTransaction){
                $orderTransaction->setProduct(null);
                $orderTransaction->setProductChoice(null);
                $this->_em->persist($orderTransaction);
            }
            foreach ($product->getProductChoices() as $productChoice){
                foreach ($productChoice->getProductOrders() as $productOrder){
                    $this->_em->remove($productOrder);
                }
                $this->_em->remove($productChoice);
            }

            $this->_em->remove($product);
        }
        $this->_em->flush();
        return true;
    }

    public function getAllActiveProductsId(): array
    {
        $activeProducts = $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->select('p.id')
            ->getQuery()
            ->getResult();

        $activeProductsId = [];
        foreach ($activeProducts as $product){
            $activeProductsId[] = $product['id'];
        }
        return $activeProductsId;
    }

    public function createFindProductsBySearchQuery($query, $translatedQuery): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->andWhere('p.title LIKE :query ')
            ->setParameter('query', '%'.$query.'%')
            ->orWhere('p.title LIKE :translatedQuery ')
            ->setParameter('translatedQuery', '%'.$translatedQuery.'%');
    }

    public function getSearchPreviewProducts($query, $translatedQuery)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->andWhere('p.title LIKE :query ')
            ->setParameter('query', '%'.$query.'%')
            ->orWhere('p.title LIKE :translatedQuery ')
            ->setParameter('translatedQuery', '%'.$translatedQuery.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
