<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\ReportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReportController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{_locale}/recipe/report/{id}', name: 'app_report_new', requirements:[ '_locale' => '%app.locales%' ])]
    public function newReport(Recipe $recipe, Request $request, ReportRepository $reportRepository): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Auto-fill info
            $report->setRecipe($recipe);
            $report->setUser($this->getUser());

            $reportRepository->add($report, true);

            return $this->redirectToRoute('app_recipe_show', [ 'slug' => $recipe->getSlug() ]);
        }

        return $this->renderForm('report/new.html.twig', [
            'form' => $form,
            'recipe' => $recipe
        ]);
    }
}
