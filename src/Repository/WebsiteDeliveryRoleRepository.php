<?php

namespace App\Repository;

use App\Entity\WebsiteDeliveryRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WebsiteDeliveryRole>
 *
 * @method WebsiteDeliveryRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebsiteDeliveryRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebsiteDeliveryRole[]    findAll()
 * @method WebsiteDeliveryRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteDeliveryRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteDeliveryRole::class);
    }

//    /**
//     * @return WebsiteDeliveryRole[] Returns an array of WebsiteDeliveryRole objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WebsiteDeliveryRole
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
