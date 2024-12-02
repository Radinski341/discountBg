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
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-home');
        // Config Group
        yield MenuItem::subMenu('Config', 'fa-solid fa-cogs')->setSubItems([
            MenuItem::linkToCrud('Website Deliver Roles', 'fa-solid fa-user-cog', WebsiteDeliveryRole::class),
            MenuItem::linkToCrud('Base Categories', 'fa-solid fa-layer-group', BaseCategory::class),
            MenuItem::linkToCrud('Base Subcategories', 'fa-solid fa-layer-group', BaseSubcategory::class),
            MenuItem::linkToCrud('Websites', 'fa-solid fa-globe', Website::class),
        ])->setPermission('ROLE_OWNER');

        // Categories Group
        yield MenuItem::subMenu('Category Management', 'fa-solid fa-sitemap')->setSubItems([
            MenuItem::linkToCrud('Main Categories', 'fa-solid fa-folder', MainCategory::class),
            MenuItem::linkToCrud('Categories', 'fa-solid fa-folder-open', Category::class),
            MenuItem::linkToCrud('Subcategories', 'fa-solid fa-folder-tree', SubCategory::class),
        ]);

        // Products Group
        yield MenuItem::subMenu('Product Management', 'fa-solid fa-box-open')->setSubItems([
            MenuItem::linkToCrud('Products', 'fa-solid fa-box', Product::class),
            MenuItem::linkToCrud('Product Choices', 'fa-solid fa-list-check', ProductChoice::class),
        ]);

        // Visual Elements (Suggested Name)
        yield MenuItem::subMenu('Visual Elements', 'fa-solid fa-image')->setSubItems([
            MenuItem::linkToCrud('Banners', 'fa-solid fa-flag', Banner::class),
            MenuItem::linkToCrud('Carousel', 'fa-solid fa-images', Carousel::class),
        ]);

        yield MenuItem::section('Sensitive')->setPermission('ROLE_OWNER');

        // Other Items
        yield MenuItem::linkToCrud('Users', 'fa-solid fa-users', User::class)->setPermission('ROLE_OWNER');
        yield MenuItem::linkToRoute('Data Upload', 'fa-solid fa-database', 'app_admin_data_upload')->setPermission('ROLE_OWNER');

        // Separator and Explanation for Orders
        // Visual Separator and Explanation for Orders Section
        yield MenuItem::section('Orders');

        // Orders Group
        yield MenuItem::linkToRoute('Pending', 'fa-solid fa-hourglass-start', 'app_admin_pending_orders');
        yield MenuItem::linkToRoute('In Process', 'fa-solid fa-spinner', 'app_admin_in_process_orders');
        yield MenuItem::linkToRoute('Ordered', 'fa-solid fa-truck', 'app_admin_ordered_orders');
        yield MenuItem::linkToRoute('Problem', 'fa-solid fa-circle-xmark', 'app_admin_problem_orders');
        yield MenuItem::linkToRoute('Closed', 'fa-solid fa-check', 'app_admin_closed_orders');
    }


    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
