<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            // Fields visible on the index page
            yield TextField::new('email', 'Email');
            yield TextField::new('phoneNumber', 'Phone Number');
            yield TextField::new('city', 'City');
            yield ChoiceField::new('roles', 'Roles')
                ->setChoices([
                    'Owner' => 'ROLE_OWNER',
                    'Head Admin' => 'ROLE_HEAD_ADMIN',
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ])
                ->renderExpanded(false)
                ->renderAsBadges();
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            // Fields visible on the detail page
            yield IdField::new('id', 'ID');
            yield TextField::new('email', 'Email');
            yield TextField::new('phoneNumber', 'Phone Number');
            yield TextField::new('address', 'Address');
            yield TextField::new('city', 'City');
            yield TextField::new('populatedPlace', 'Populated Place');
            yield TextField::new('fullName', 'Full Name');
            yield ChoiceField::new('roles', 'Roles')
                ->setChoices([
                    'Owner' => 'ROLE_OWNER',
                    'Head Admin' => 'ROLE_HEAD_ADMIN',
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ])
                ->renderExpanded(false)
                ->renderAsBadges();
        }

        if ($pageName === Crud::PAGE_EDIT) {
            yield TextField::new('email', 'Email Address')
                ->setRequired(true);

            yield TextField::new('phoneNumber', 'Phone Number')
                ->setRequired(true);

            yield TextField::new('address', 'Address')
                ->setRequired(true);

            yield TextField::new('city', 'City')
                ->setRequired(true);

            yield TextField::new('populatedPlace', 'Populated Place')
                ->setRequired(true);

            yield ChoiceField::new('roles', 'Roles')
                ->setChoices([
                    'Owner' => 'ROLE_OWNER',
                    'Head Admin' => 'ROLE_HEAD_ADMIN',
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ])
                ->allowMultipleChoices()
                ->renderExpanded(false)
                ->renderAsBadges();
        }
    }
}
