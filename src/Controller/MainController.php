<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Service\TrendingContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home_without_locale')]
    public function homeWithoutLocale(): Response {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/{_locale}', name: 'app_home', requirements:[ '_locale' => '%app.locales%' ])]
    public function home(TrendingContainer $trendingContainer): Response
    {
        return $this->render('main/home.html.twig', [
            'recipes' => $trendingContainer->getTrendingRecipes()
        ]);
    }

    #[Route('/ingredients/search', name: 'app_ingredients_search_json')]
    public function searchIngredients(Request $request, IngredientRepository $ingredientRepository): Response {
        $ingredients = $ingredientRepository->search($request->get('query', ''));
        $response = [];
        foreach ($ingredients as $key => $ingredient) {
            $response[$key]['id'] = $ingredient->getId();
            $response[$key]['label'] = $ingredient->getName();
            $response[$key]['emoji'] = $ingredient->getEmoji() ?? $ingredient->getCategory()->getDefaultEmoji();
            $response[$key]['unit'] = $ingredient->getUnit()->getName();
        }

        return $this->json($response);
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', defaults:[ '_format' => 'xml' ])]
    public function showAction(Request $request, RecipeRepository $recipeRepository) {
        $urls = [];
        $hostname = $request->getSchemeAndHttpHost();
 
        // add static urls
        $urls[] = [ 'loc' => $this->generateUrl('app_home'), 'priority' => 1.0 ];
        $urls[] = [ 'loc' => $this->generateUrl('app_search_builder'), 'priority' => 0.9 ];
        $urls[] = [ 'loc' => $this->generateUrl('app_recipe_new'), 'priority' => 0.7 ];

        $urls[] = [ 'loc' => $this->generateUrl('app_login'), 'priority' => 0.5 ];
        $urls[] = [ 'loc' => $this->generateUrl('app_register'), 'priority' => 0.5 ];
        $urls[] = [ 'loc' => $this->generateUrl('app_account'), 'priority' => 0.4 ];
         
        // add dynamic urls, like blog posts from your DB
        foreach ($recipeRepository->findAll() as $recipe) {
            $url = [
                'loc' => $this->generateUrl('app_recipe_show', [ 'id' => $recipe->getId() ]),
                'lastmod' => $recipe->getCreatedAt()->format('c'),
                'changefreq' => 'always',
                'priority' => 0.7
            ];

            if (!empty($recipe->getImageUrl())) {
                $url['image'] = [
                    'loc' => '/upload/' . $recipe->getImageUrl(),
                    'title' => $recipe->getTitle()
                ];
            }

            $urls[] = $url;
        }
       
 
        // return response in XML format
        $response = new Response(
            $this->renderView('sitemap/sitemap.html.twig', array( 'urls' => $urls,
                'hostname' => $hostname)),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
 
    }
}
