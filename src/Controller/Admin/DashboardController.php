<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\BaseCategory;
use App\Entity\BaseSubcategory;
use App\Entity\Carousel;
use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\MainCategory;
use App\Entity\Product;
use App\Entity\ProductChoice;
use App\Entity\SubCategory;
use App\Entity\User;
use App\Entity\Website;
use App\Entity\WebsiteDeliveryRole;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('/admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Discount Bg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Banners', 'fa fa-list', Banner::class);
        yield MenuItem::linkToCrud('Websites', 'fa fa-list', Website::class);
        yield MenuItem::linkToCrud('Website deliver roles', 'fa fa-list', WebsiteDeliveryRole::class);
        yield MenuItem::linkToCrud('Base Categories', 'fa fa-list', BaseCategory::class);
        yield MenuItem::linkToCrud('Base SubCategories', 'fa fa-list', BaseSubcategory::class);
        yield MenuItem::linkToCrud('Main Categories', 'fa fa-list', MainCategory::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Sub Categories', 'fa fa-list', SubCategory::class);
        yield MenuItem::linkToCrud('Products', 'fa fa-list', Product::class);
        yield MenuItem::linkToCrud('Product Choices', 'fa fa-list', ProductChoice::class);
        yield MenuItem::linkToCrud('Users', 'fa-solid fa-users', User::class);
        yield MenuItem::linkToCrud('Cart', 'fa fa-shopping-cart', Cart::class);
        yield MenuItem::linkToCrud('Carousel', 'fa fa-shopping-cart', Carousel::class);
        yield MenuItem::linkToRoute('Data upload', 'fa-solid fa-database', 'app_admin_data_upload');
        yield MenuItem::subMenu('Orders', 'fa-solid fa-file-invoice')->setSubItems([
            MenuItem::linkToRoute('Pending', 'fa-solid fa-hourglass-start', 'app_admin_pending_orders'),
            MenuItem::linkToRoute('In process', 'fa-solid fa-spinner', 'app_admin_in_process_orders'),
            MenuItem::linkToRoute('Ordered', 'fa-solid fa-truck', 'app_admin_ordered_orders'),
            MenuItem::linkToRoute('Problem', 'fa-solid fa-circle-xmark', 'app_admin_problem_orders'),
            MenuItem::linkToRoute('Closed', 'fa-solid fa-check', 'app_admin_closed_orders')
        ]);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
