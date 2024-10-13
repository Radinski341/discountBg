<?php

namespace App\Repository;

use App\Entity\FavouriteOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavouriteOrder>
 *
 * @method FavouriteOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavouriteOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavouriteOrder[]    findAll()
 * @method FavouriteOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouriteOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavouriteOrder::class);
    }

//    /**
//     * @return FavouriteOrder[] Returns an array of FavouriteOrder objects
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

//    public function findOneBySomeField($value): ?FavouriteOrder
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
