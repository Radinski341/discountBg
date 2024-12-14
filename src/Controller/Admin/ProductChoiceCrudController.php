<?php

namespace App\Controller\Admin;

use App\Entity\ProductChoice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductChoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductChoice::class;
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_EDIT) {
            yield TextField::new('title', 'Title')
                ->setRequired(true);

            yield NumberField::new('newPrice', 'New Price')
                ->setRequired(true)
                ->setFormTypeOption('attr', ['step' => '0.01']);

            yield TextareaField::new('images', 'Images')
                ->setRequired(false)
                ->setHelp('Enter image URLs separated by a delimiter.');

        }

        if ($pageName === Crud::PAGE_INDEX) {
            // Exclude these fields from the index page
            yield IdField::new('id');
            yield TextField::new('websiteName', 'Website Name');
            yield TextField::new('websiteId', 'Website ID');
            yield TextField::new('optionType', 'Option Type');
            yield TextField::new('optionValue', 'Option Value');
            yield TextField::new('title', 'Title');
            yield NumberField::new('oldPrice', 'Old Price');
            yield NumberField::new('newPrice', 'New Price');
            yield NumberField::new('discountPercent', 'Discount Percent');
            yield TextField::new('productUrl', 'Product URL');
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            // Show everything on the detail page
            yield IdField::new('id');
            yield TextField::new('websiteName', 'Website Name');
            yield TextField::new('websiteId', 'Website ID');
            yield TextField::new('optionType', 'Option Type');
            yield TextField::new('optionValue', 'Option Value');
            yield TextField::new('title', 'Title');
            yield NumberField::new('oldPrice', 'Old Price');
            yield NumberField::new('newPrice', 'New Price');
            yield NumberField::new('discountPercent', 'Discount Percent');
            yield TextField::new('productUrl', 'Product URL');
            yield TextareaField::new('images', 'Images');
            yield BooleanField::new('forDelete', 'For Delete');
            yield NumberField::new('originalDiscountPercent', 'Original Discount Percent');
            yield BooleanField::new('isFavourite', 'Is Favourite');
        }
    }

}
