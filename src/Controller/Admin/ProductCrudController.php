<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Products')
            ->setPageTitle(Crud::PAGE_INDEX, 'Products List')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit Product')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Product Details')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            yield IdField::new('id');
            yield TextField::new('websiteName', 'Website Name');
            yield TextField::new('websiteId', 'Website ID');
            yield TextField::new('productUrl', 'Product URL');
            yield TextField::new('title', 'Title');
            yield NumberField::new('oldPrice', 'Old Price');
            yield NumberField::new('newPrice', 'New Price');
            yield NumberField::new('discountPercent', 'Discount Percent');
            yield NumberField::new('deliveryPrice', 'Delivery Price');
            yield BooleanField::new('isActive', 'Is Active');
        }

        if ($pageName === Crud::PAGE_EDIT) {
            yield TextField::new('title', 'Title');

            yield BooleanField::new('isActive', 'Is Active')->renderAsSwitch(false);

            // Prices section
            yield NumberField::new('oldPrice', 'Old Price')->setRequired(true)->setFormTypeOption('attr', ['step' => '0.01']);
            yield NumberField::new('newPrice', 'New Price')->setRequired(true)->setFormTypeOption('attr', ['step' => '0.01']);
            yield NumberField::new('deliveryPrice', 'Delivery Price')->setRequired(true)->setFormTypeOption('attr', ['step' => '0.01']);

            yield TextareaField::new('images', 'Product Images (as text)');
            yield TextEditorField::new('description', 'Description')->setFormType(TextEditorType::class)->setNumOfRows(10);


        }

        if ($pageName === Crud::PAGE_DETAIL) {
            yield IdField::new('id');
            yield TextField::new('websiteName', 'Website Name');
            yield TextField::new('websiteUrl', 'Website URL');
            yield TextField::new('websiteId', 'Website ID');
            yield TextField::new('productUrl', 'Product URL');
            yield TextField::new('title', 'Title');
            yield SlugField::new('slug')->setTargetFieldName('title');
            yield NumberField::new('oldPrice', 'Old Price');
            yield NumberField::new('newPrice', 'New Price');
            yield NumberField::new('originalDiscountPrice', 'Original Discount Price');
            yield NumberField::new('originalDiscountPercent', 'Original Discount Percent');
            yield NumberField::new('discountPercent', 'Discount Percent');
            yield TextareaField::new('description', 'Description');
            yield AssociationField::new('category');
            yield AssociationField::new('subCategory');
            yield BooleanField::new('isActive', 'Is Active');
            yield CollectionField::new('productChoices', 'Product Choices')->allowAdd()->allowDelete();
            yield CollectionField::new('productOrders', 'Product Orders')->allowAdd()->allowDelete();
            yield CollectionField::new('favouriteOrders', 'Favourite Orders')->allowAdd()->allowDelete();
            yield NumberField::new('deliveryPrice', 'Delivery Price');
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Product) {
            $entityInstance->setDiscountPercent(
                $this->calculateDiscountPercent($entityInstance->getOldPrice(), $entityInstance->getNewPrice())
            );
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Product) {
            $entityInstance->setDiscountPercent(
                $this->calculateDiscountPercent($entityInstance->getOldPrice(), $entityInstance->getNewPrice())
            );
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function calculateDiscountPercent(float $oldPrice, float $newPrice): float
    {
        if ($oldPrice == 0) {
            return 0;
        }

        return round((($oldPrice - $newPrice) / $oldPrice) * 100, 2);
    }

}
