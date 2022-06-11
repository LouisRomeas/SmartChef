<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;

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