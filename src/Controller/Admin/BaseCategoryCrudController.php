<?php

namespace App\Controller\Admin;

use App\Entity\BaseCategory;
use App\Entity\Website;
use App\Repository\WebsiteRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BaseCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BaseCategory::class;
    }

    public function __construct(private WebsiteRepository $websiteRepository) {}

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title');
        yield ChoiceField::new('website')
            ->setChoices($this->getWebsiteChoices());
        yield TextField::new('replaceCategory');
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
