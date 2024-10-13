<?php

namespace App\Controller\Admin;

use App\Entity\Carousel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarouselCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carousel::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            IntegerField::new('priority'),
            AssociationField::new('products')->setFormTypeOptions([
                'by_reference' => false,
            ])
        ];
    }

}
