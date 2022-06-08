<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Component\Form\FormInterface;

class SearchContainer {
  public function __construct(private RecipeRepository $recipeRepository)
  {
    
  }

  /**
   * @return Recipe[]
   */
  public function search($formData): array {
    $recipes = $this->recipeRepository->getExactMatches(
      $formData['recipeIngredients'],
      $formData['portions'],
    );
    return $recipes;
  }
}