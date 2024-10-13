<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private ProductRepository $productRepository;
    private SubCategoryRepository $subCategoryRepository;
    private ProductChoiceRepository $productChoiceRepository;

    public function __construct(ManagerRegistry $registry, ProductRepository $productRepository, SubCategoryRepository $subCategoryRepository, ProductChoiceRepository $productChoiceRepository)
    {
        parent::__construct($registry, Category::class);
        $this->productRepository = $productRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->productChoiceRepository = $productChoiceRepository;
    }

    public function findAllCategoriesWithoutSubcategory(): array
    {
        $allCategories =  $this->createQueryBuilder('c')
            ->select(['c.title'])
            ->getQuery()
            ->getResult()
        ;
        $allCategoryLevels= [];
        foreach ($allCategories as $category){
            $allCategoryLevels[] = [
                'category' => $category['title'],
                'subCategory' => null
            ];
        }
        return $allCategoryLevels;
    }

    public function getCategoriesWithSubcategories()
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.subCategories', 's')
            ->getQuery()
            ->getResult();
    }

    public function getCategoriesWithoutMainCategory()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.mainCategory IS NULL')
            ->innerJoin('c.subCategories', 's')
            ->getQuery()
            ->getResult();
    }

    public function deleteALlMissingCategories(): bool
    {
        $categoriesForDelete = $this->findBy(['forDelete' => true]);
        if(count($categoriesForDelete) === 0){
            return false;
        }
        foreach ($categoriesForDelete as $category){
            foreach ($category->getSubCategories() as $subCategory){
                foreach ($subCategory->getProducts() as $product){
                    foreach ($product->getProductChoices() as $productChoice){
                        $this->_em->remove($productChoice);
                    }
                    $this->_em->remove($product);
                }
                $this->_em->remove($subCategory);
            }
            $this->_em->remove($category);
        }
        $this->_em->flush();

        return true;
    }

    public function refreshForDeleteField()
    {
        if(count($this->findAll()) > 0) {
            return $this->createQueryBuilder('c')
                ->update()
                ->set('c.forDelete', true)
                ->getQuery()
                ->execute();
        }
    }
//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
