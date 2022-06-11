<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;

class TrendingContainer {
  public function __construct(private RecipeRepository $recipeRepository)
  {
    
  }

  /**
   * @return Recipe[]
   */
  public function getTrendingRecipes(int $limit = null, int $offset = null): array {
    $recipes = $this->recipeRepository->findBy([], null, $limit, $offset);
    return $recipes;
  }
}