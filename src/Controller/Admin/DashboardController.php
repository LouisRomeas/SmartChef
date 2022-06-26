<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Controller\Admin\UserCrudController;
use App\Entity\Recipe;
use App\Entity\Report;
use App\Entity\ReportReason;
use App\Entity\Unit;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SmartChef');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('admin.dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('admin.backToFront', 'fas fa-right-from-bracket', 'app_login');
        yield MenuItem::section('admin.backOffice', 'fa-solid fa-shop');
        yield MenuItem::linkToCrud('entity.user._plural', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('entity.category._plural', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('entity.ingredient._plural', 'fas fa-apple-whole', Ingredient::class);
        yield MenuItem::linkToCrud('entity.reportReason._plural', 'fas fa-flag', ReportReason::class);
        yield MenuItem::linkToCrud('entity.unit._plural', 'fas fa-scale-balanced', Unit::class);
        yield MenuItem::section('admin.moderation', 'fas fa-shield-halved');
        yield MenuItem::linkToCrud('entity.report._plural', 'fas fa-flag', Report::class);
        yield MenuItem::linkToCrud('entity.recipe._plural', 'fa-solid fa-newspaper', Recipe::class);
    }
}
