<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BannerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Banner::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('url')
            ->setLabel('URL');

        yield DateTimeField::new('createdAt')
            ->setLabel('Created At')
            ->hideOnForm();

        yield DateTimeField::new('updatedAt')
            ->setLabel('Updated At')
            ->hideOnForm();

        $imageField = ImageField::new('img')
            ->setUploadDir('public/uploads/images')
            ->setBasePath('uploads/images');

        if (Crud::PAGE_EDIT === $pageName) {
            $imageField->setRequired(false); // Ensure it's not mandatory during edit
        }

        yield $imageField;

    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Banner) {
            $now = new \DateTimeImmutable();
            $nowMutable = new \DateTime();
            $entityInstance->setCreatedAt($now);
            $entityInstance->setUpdatedAt($nowMutable); // Set updatedAt as well on creation
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    // Override updateEntity to set updatedAt on update
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Banner) {
            $now = new \DateTime();
            $entityInstance->setUpdatedAt($now); // Set updatedAt on every update
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

}
