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
            ->setParameter('website', $website)
            ->select('p.websiteId')
            ->getQuery()
            ->getResult();

        return array_column($allProducts, 'websiteId');
    }

    public function createFindAllActiveProductsQueryBuilder(?Category $category, ?SubCategory $subCategory, Request $request, ?MainCategory $mainCategory = null): QueryBuilder
    {
        $queryBuilder =  $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true');

        if ($mainCategory) {
            $categories = $mainCategory->getCategories();

            if (!empty($categories)) {
                $queryBuilder->andWhere('p.category IN (:categories)')
                    ->setParameter('categories', $categories);
            }
        } elseif ($category) {
            $queryBuilder->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        if ($subCategory) {
            $queryBuilder->andWhere('p.subCategory = :subCategory')
                ->setParameter('subCategory', $subCategory);
        }

        if ($order = $request->get('order')) {
            switch ($order) {
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

        if ($request->get('priceRangeFrom') || $request->get('priceRangeTo')) {
            $queryBuilder
                ->andWhere('p.newPrice >= :priceRangeFrom')
                ->setParameter('priceRangeFrom', $request->get('priceRangeFrom') ?? 0)
                ->andWhere('p.newPrice <= :priceRangeTo')
                ->setParameter('priceRangeTo', $request->get('priceRangeTo') ?? PHP_INT_MAX);
        }

        return $queryBuilder;
    }

    public function findAllProductsWithChoices(array $productIds)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:productIds)')
            ->setParameter('productIds', array_column($productIds, 'id'))
            ->getQuery()
            ->getResult();
    }

    public function getProductWithChoice($productSlug, $optionValue)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.productChoices', 'pc')
            ->where('p.slug = :productSlug')
            ->setParameter('productSlug', $productSlug)
            ->andWhere('pc.optionValue = :optionValue')
            ->setParameter('optionValue', $optionValue)
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
        return $this->createQueryBuilder('p')
            ->where('p.slug = :productSlug')
            ->setParameter('productSlug', $productSlug)
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
        return $this->createQueryBuilder('p')
            ->update()
            ->set('p.forDelete', 'true')
            ->getQuery()
            ->execute();
    }

    public function preventFromDelete()
    {
        return $this->createQueryBuilder('p')
            ->update()
            ->set('p.forDelete', 'false')
            ->getQuery()
            ->execute();
    }

    public function getNumberOfProductsForDelete($website)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.forDelete = :forDelete')
            ->andWhere('p.websiteName = :websiteName')
            ->setParameter('forDelete', true)
            ->setParameter('websiteName', $website);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function deleteAllMissingProducts($website): array
    {
        $productsForDelete = $this->findBy(['forDelete' => true, 'websiteName' => $website],null, 200);

        if (count($productsForDelete) === 0) {
            return ['removedProducts' => 0, 'removedChoices' => 0];
        }
        $batchLimit = 50;
        $batchCount = 0;
        $removedProducts = 0;
        $removedChoices = 0;
        foreach ($productsForDelete as $product) {
            foreach ($product->getProductOrders() as $productOrder) {
                $this->_em->remove($productOrder);
            }

            foreach ($product->getOrderTransactions() as $orderTransaction) {
                $orderTransaction->setProduct(null);
                $orderTransaction->setProductChoice(null);
                $this->_em->persist($orderTransaction);
            }

            foreach ($product->getProductChoices() as $productChoice) {
                foreach ($productChoice->getProductOrders() as $productOrder) {
                    $this->_em->remove($productOrder);
                }
                $this->_em->remove($productChoice);
                $removedChoices++;
            }
            $this->_em->remove($product);
            $removedProducts++;
            if($batchCount++ >= $batchLimit) {
                $this->_em->flush();
                $batchCount = 0;
            }
        }
        $this->_em->flush(); // Remove rest stacked in the batch
        return [
            'removedProducts' => $removedProducts,
            'removedChoices' => $removedChoices
        ];
    }

    public function getAllActiveProductsId(): array
    {
        $activeProducts = $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->select('p.id')
            ->getQuery()
            ->getResult();

        return array_column($activeProducts, 'id');
    }

    public function createFindProductsBySearchQuery($query, $translatedQuery): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->andWhere('p.title ILIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orWhere('p.title ILIKE :translatedQuery')
            ->setParameter('translatedQuery', '%' . $translatedQuery . '%');
    }

    public function getSearchPreviewProducts($query, $translatedQuery)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = true')
            ->andWhere('p.title ILIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orWhere('p.title ILIKE :translatedQuery')
            ->setParameter('translatedQuery', '%' . $translatedQuery . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function deleteProductChoicesForMarkedProducts(string $website): void
    {
        $qb = $this->createQueryBuilder('p');
        $qb->delete('App\Entity\ProductChoice', 'pc')
            ->where('pc.product IN (
            SELECT p.id FROM App\Entity\Product p WHERE p.forDelete = true AND p.websiteName = :website
        )')
            ->setParameter('website', $website)
            ->getQuery()
            ->execute();
    }
}
