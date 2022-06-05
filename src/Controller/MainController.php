<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/ingredients/search', name: 'app_ingredients_search_json')]
    public function searchIngredients(Request $request, IngredientRepository $ingredientRepository): Response {
        $ingredients = $ingredientRepository->search($request->get('query', ''));
        $response = [];
        foreach ($ingredients as $key => $ingredient) {
            $response[$key]['id'] = $ingredient->getId();
            $response[$key]['label'] = $ingredient->getName();
            $response[$key]['emoji'] = $ingredient->getEmoji() ?? null;
            $response[$key]['unit'] = $ingredient->getUnit()->getName();
        }

        return $this->json($response);
    }
}
