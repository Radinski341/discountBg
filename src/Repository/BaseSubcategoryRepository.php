<?php

namespace App\Repository;

use App\Entity\BaseSubcategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BaseSubcategory>
 *
 * @method BaseSubcategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BaseSubcategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BaseSubcategory[]    findAll()
 * @method BaseSubcategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseSubcategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseSubcategory::class);
    }

//    /**
//     * @return BaseSubcategory[] Returns an array of BaseSubcategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BaseSubcategory
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
