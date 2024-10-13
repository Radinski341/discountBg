<?php

namespace App\Repository;

use App\Entity\Favourite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favourite>
 *
 * @method Favourite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favourite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favourite[]    findAll()
 * @method Favourite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favourite::class);
    }

    public function getFavouriteOrderProductsId(): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->innerJoin('f.favouriteOrders', 'fo')
            ->innerJoin('fo.product', 'p')
            ->select('p.id')
            ->getQuery()
            ->getResult();
        $productsId = [];
        foreach ($queryBuilder as $product){
            $productsId[] = $product['id'];
        }
        return $productsId;
    }
    public function getFavouriteOrderProductChoicesId(): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->innerJoin('f.favouriteOrders', 'fo')
            ->innerJoin('fo.productChoice', 'pc')
            ->select('pc.id')
            ->getQuery()
            ->getResult();
        $productsId = [];
        foreach ($queryBuilder as $product){
            $productsId[] = $product['id'];
        }
        return $productsId;
    }

//    /**
//     * @return Favourite[] Returns an array of Favourite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Favourite
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
