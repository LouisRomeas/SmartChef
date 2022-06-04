<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Controller\Admin\UserCrudController;
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
    public function __construct(private TranslatorInterface $translator)
    {
        
    }

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
        yield MenuItem::linkToDashboard($this->translator->trans('Dashboard'), 'fa fa-home');
        yield MenuItem::linkToCrud($this->translator->trans('Users'), 'fas fa-user', User::class);
        yield MenuItem::linkToCrud($this->translator->trans('Categories'), 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud($this->translator->trans('Ingredients'), 'fas fa-apple-whole', Ingredient::class);
        yield MenuItem::linkToCrud($this->translator->trans('Report reasons'), 'fas fa-flag', ReportReason::class);
        yield MenuItem::linkToCrud($this->translator->trans('Units of measurement'), 'fas fa-scale-balanced', Unit::class);
        yield MenuItem::linkToRoute($this->translator->trans('Back to front'), 'fas fa-right-from-bracket', 'app_home');
    }
}
