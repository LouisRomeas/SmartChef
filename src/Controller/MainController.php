<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home_without_locale')]
    #[Route('/{_locale}', name: 'app_home', requirements:[ '_locale' => '%app.locales%' ])]
    public function home(): Response
    {
        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
