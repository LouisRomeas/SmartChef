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

    /**
     * @param Recipe $recipe1
     * @param Recipe $recipe2
     */
    usort($recipes, function($recipe1, $recipe2) {
      return $recipe2->getScore() <=> $recipe1->getScore();
    });

    return $recipes;
  }
}