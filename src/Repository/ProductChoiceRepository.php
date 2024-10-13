<?php

namespace App\Repository;

use App\Entity\ProductChoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductChoice>
 *
 * @method ProductChoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductChoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductChoice[]    findAll()
 * @method ProductChoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductChoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductChoice::class);
    }

    public function findAllProductsWebsiteId(): array
    {
        $allProducts =  $this->createQueryBuilder('p')
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

    public function findALlProductsWithChoices()
    {
        $queryBuilder = $this->createQueryBuilder('pc');
        $productIds = $queryBuilder
            ->innerJoin('pc.product', 'p')
            ->select(['p.id'])
            ->getQuery()
            ->getResult();
        $products = [];
        foreach ($productIds as $productId){
            if(!in_array($productId, $products)){
                $products[] = $productId;
            }
        }
        return $products;
    }

    public function deleteALlMissingProductChoices($website): void
    {
        $productChoicesForDelete = $this->findBy(['forDelete' => true, 'websiteName' => $website]);
        foreach ($productChoicesForDelete as $productChoice){
            foreach ($productChoice->getOrderTransactions() as $orderTransaction){
                $orderTransaction->setProductChoice(null);
            }
            foreach ($productChoice->getProductOrders() as $productOrder){
                $this->_em->remove($productOrder);
            }
            $this->_em->remove($productChoice);
        }
        $this->_em->flush();
    }

    public function refreshForDeleteField()
    {
        if(count($this->findAll()) > 0) {
            dump("CHOICE REFRESH");
            return $this->createQueryBuilder('p')
                ->update()
                ->set('p.forDelete', true)
                ->getQuery()
                ->execute();
        }
    }

    public function preventFromDelete()
    {
        if(count($this->findAll()) > 0) {
            dump("CHOICE PREVENT");
            return $this->createQueryBuilder('p')
                ->update()
                ->set('p.forDelete', 'false')
                ->getQuery()
                ->execute();
        }
    }

//    /**
//     * @return ProductChoice[] Returns an array of ProductChoice objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProductChoice
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
