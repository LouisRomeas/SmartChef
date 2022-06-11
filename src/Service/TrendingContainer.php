<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTime;
use DateTimeInterface;

class TrendingContainer {
  public function __construct(private RecipeRepository $recipeRepository)
  {
    
  }

  /**
   * @return Recipe[]
   */
  public function getTrendingRecipes(?DateTimeInterface $earliest = null, int $limit = null, int $offset = null): array {
    $recipes = $this->recipeRepository->findRecent($earliest ?? new DateTime('7 days ago'), $limit, $offset);
    return $recipes;
  }
}