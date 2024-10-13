<?php

namespace App\Controller\Admin;

use App\Entity\MainCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MainCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MainCategory::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();
        yield TextField::new('title', 'Title');
        yield SlugField::new('slug', 'Slug')
            ->setTargetFieldName('title');
        yield ImageField::new('img', 'Image')
            ->setUploadDir('public/uploads/images')
            ->setBasePath('uploads/images');
    }

}
