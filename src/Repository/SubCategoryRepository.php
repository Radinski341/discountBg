<?php

namespace App\Repository;

use App\Entity\SubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubCategory>
 *
 * @method SubCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubCategory[]    findAll()
 * @method SubCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubCategoryRepository extends ServiceEntityRepository
{
    private ProductRepository $productRepository;

    public function __construct(ManagerRegistry $registry, ProductRepository $productRepository)
    {
        parent::__construct($registry, SubCategory::class);
        $this->productRepository = $productRepository;
    }

    public function findAllCategoryLevels(): array
    {
        $allCategories =  $this->createQueryBuilder('s')
            ->join('s.category', 'c')
            ->addSelect('c.title')
            ->getQuery()
            ->getResult()
        ;

        $allCategoryLevels= [];
        foreach ($allCategories as $category){
            $allCategoryLevels[] = [
                'category' => $category['title'],
                'subCategory' => $category[0]->getTitle()
            ];
        }
        return $allCategoryLevels;
    }

    public function deleteALlMissingSubCategories(): bool
    {

        $subCategoriesForDelete = $this->findBy(['forDelete' => true]);
        if(count($subCategoriesForDelete) === 0){
            return false;
        }
        $this->productRepository->preventFromDelete();
        foreach ($subCategoriesForDelete as $subCategory){
            foreach ($subCategory->getProducts() as $product){
                foreach ($product->getProductOrders() as $productOrder){
                    $this->_em->remove($productOrder);
                }
                foreach ($product->getProductChoices() as $productChoice){
                    foreach ($productChoice->getProductOrders() as $productOrder){
                        $this->_em->remove($productOrder);
                    }
                    $this->_em->persist($productChoice);
                }
                $this->_em->persist($product);
            }
            $this->_em->remove($subCategory);
        }
        $this->_em->flush();
        return true;
    }

    public function refreshForDeleteField()
    {
        if(count($this->findAll()) > 0){
            return $this->createQueryBuilder('s')
            ->update()
            ->set('s.forDelete', true)
            ->getQuery()
            ->execute();
       }
    }

    public function preventFromDelete()
    {

        if(count($this->findAll()) > 0){
            return  $this->createQueryBuilder('s')
            ->update()
            ->set('s.forDelete', 'false')
            ->getQuery()
            ->execute();
        }
    }
//    /**
//     * @return SubCategory[] Returns an array of SubCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SubCategory
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
