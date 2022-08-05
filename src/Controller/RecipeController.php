<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Form\SearchType;
use App\Service\SearchContainer;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('{_locale}/recipe', requirements:[ '_locale' => '%app.locales%' ])]
class RecipeController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger
    )
    {
        
    }

    /**
     * @return string Full final filepath of thumbnail
     */
    private function handleThumbnailInput(Recipe $recipe, UploadedFile $imageFile): string {
        // $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        // $safeFilename = $this->slugger->slug($originalFilename);
        $destinationDir = $this->getParameter('upload_directory') . '/recipe';

        foreach (scandir($destinationDir) as $file) {
            $filepath = $destinationDir . '/' . $file;
            if ( is_file($filepath) && strpos( $file, $recipe->getId() . '-' ) === 0 ) unlink($filepath);
        }

        $newFilename = $recipe->getId() . '-' . time() . '-' . uniqid() . '.' . $imageFile->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $imageFile->move(
                $destinationDir,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $newFilename;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecipeRepository $recipeRepository): Response
    {

        if (!$this->isGranted('ROLE_EDITOR')) {
            return new Response($this->renderView('errors/forbidden.html.twig', [
                'role_needed' => 'admin.roles.editor'
            ]), 403);
        }

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setAuthor($this->getUser());
            $recipe->setSlug( $this->slugger->slug($recipe->getId() . " " . strtolower($recipe->getTitle())) );

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageUrl')->getData();

            if ($imageFile) $recipe->setImageUrl($this->handleThumbnailInput($recipe, $imageFile));

            foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                $recipeIngredient->setRecipe($recipe);
            }

            $recipeRepository->add($recipe, true);

            return $this->redirectToRoute('app_recipe_show', [ 'id' => $recipe->getId() ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_search_builder', methods: ['GET', 'POST'])]
    public function searchBuilder(Request $request, SearchContainer $searchContainer): Response {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipes = $searchContainer->search($form->getData());

            return $this->render('main/search_results.html.twig', [
                'recipes' => $recipes,
                'recipeIngredients' => $form->getData()['recipeIngredients'],
                'portions' => $form->getData()['portions']
            ]);
        }

        return $this->renderForm('main/search.html.twig', [
            'form' => $form,
        ]);
    }

    // Routes starting with a recipe's ID

    #[Route('/{slug}', name: 'app_recipe_show', methods: ['GET'])]
    public function show(Recipe $recipe, RecipeRepository $recipeRepository, SessionInterface $session): Response
    {
        // Increment view count on Recipe if not already viewed within same session

        /** @var int[] $viewedRecipes */
        $viewedRecipes = $session->get('viewed_recipes', []);

        if (!in_array($recipe->getId(), $viewedRecipes)) {
            $recipe->addView();
            $recipeRepository->add($recipe, true);
            $viewedRecipes[] = $recipe->getId();
            $session->set('viewed_recipes', $viewedRecipes);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[IsGranted('ROLE_EDITOR')]
    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, RecipeRepository $recipeRepository, SluggerInterface $slugger): Response
    {
        if ($this->getUser() !== $recipe->getAuthor()) return $this->redirectToRoute('app_home', status: 401);

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageUrl')->getData();

            if ($imageFile) $recipe->setImageUrl($this->handleThumbnailInput($recipe, $imageFile));

            $recipeRepository->add($recipe, true);

            return $this->redirectToRoute('app_recipe_show', [ 'id' => $recipe->getId() ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
