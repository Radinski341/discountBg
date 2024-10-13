<?php

namespace App\Controller\Admin;

use App\Entity\BaseSubcategory;
use App\Repository\WebsiteRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BaseSubcategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BaseSubcategory::class;
    }

    public function __construct(private WebsiteRepository $websiteRepository) {}

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title');
        yield ChoiceField::new('website')
            ->setChoices($this->getWebsiteChoices());
        yield TextField::new('category');
        yield TextField::new('replaceSubCategory')
            ->setLabel('Original subcategory name')
            ->setRequired(true);
    }

    private function getWebsiteChoices(): array
    {
        $websites = $this->websiteRepository->findAll();
        $choices = [];
        foreach ($websites as $website) {
            $choices[$website->getWebsiteName()] = $website->getWebsiteName();
        }
        return $choices;
    }
}
