<?php

namespace App\Controller\Admin;

use App\Entity\WebsiteDeliveryRole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WebsiteDeliveryRoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WebsiteDeliveryRole::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('website'),
            NumberField::new('min'),
            NumberField::new('max'),
            NumberField::new('deliveryPrice'),
        ];
    }

}
