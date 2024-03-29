<?php

namespace App\Controller\Admin;

use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Report;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\ReportReason;
use App\Controller\Admin\UserCrudController;
use App\Service\StatsContainer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private TranslatorInterface $translator, private StatsContainer $statsContainer)
    {
        
    }
    #[Route('{_locale}/admin/', name: 'admin', requirements:[ '_locale' => '%app.locales%' ])]
    public function index(): Response
    {
        if (!(
            $this->isGranted('ROLE_ADMIN') ||
            $this->isGranted('ROLE_MAINTAINER') ||
            $this->isGranted('ROLE_MODERATOR')
        )) throw $this->createAccessDeniedException();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/index.html.twig', [
            'user' => $this->getUser(),
            'stats' => $this->statsContainer->getStats()
        ]);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('layout', 'admin/layout.html.twig')
        ;
    }

    // Set top left title to correspond highest role user may have
    public function configureDashboard(): Dashboard
    {
        $title = 'SmartChef';

        foreach ([
            'ROLE_ADMIN',
            'ROLE_MODERATOR',
            'ROLE_MAINTAINER'
        ] as $role) {
            if ($this->isGranted($role)) $title = $this->getTranslationKeyFromRole($role);
            break;
        }

        return Dashboard::new()
            ->setTitle($this->translator->trans($title));
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('admin.dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('admin.backToFront', 'fas fa-right-from-bracket', 'app_login');
        yield MenuItem::section('admin.backOffice', 'fa-solid fa-shop');

        if ($this->isGranted('ROLE_MAINTAINER')) {
            yield MenuItem::linkToCrud('entity.category._plural', 'fas fa-list', Category::class);
            yield MenuItem::linkToCrud('entity.ingredient._plural', 'fas fa-apple-whole', Ingredient::class);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('entity.user._plural', 'fas fa-user', User::class);
            yield MenuItem::linkToCrud('entity.reportReason._plural', 'fas fa-flag', ReportReason::class);
            yield MenuItem::linkToCrud('entity.unit._plural', 'fas fa-scale-balanced', Unit::class);
        }

        if ($this->isGranted('ROLE_MODERATOR')) {
            yield MenuItem::section('admin.moderation', 'fas fa-shield-halved');
            yield MenuItem::linkToCrud('entity.report._plural', 'fas fa-flag', Report::class);
            yield MenuItem::linkToCrud('entity.recipe._plural', 'fa-solid fa-newspaper', Recipe::class);
        }
    }

    private function getTranslationKeyFromRole(string $role): string {
        return 'admin.roles.' . strtolower(preg_replace("/^ROLE_([A-Za-z]+)$/", "$1", $role));
    }
}
