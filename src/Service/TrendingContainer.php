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
  public function getTrendingRecipes(?DateTimeInterface $earliest = null, int $limit = 12, int $offset = null): array {
    $recipes = $this->recipeRepository->findRecent($earliest ?? (new DateTime())->setTimestamp(0), $limit, $offset);

    // Filter out results
    foreach ($recipes as $key => $recipe) {
      // Removes undesirable recipes
      if (
        $recipe->getScore() < 0
      ) unset($recipes[$key]);
    }

    /** @param Recipe $recipe1
     *  @param Recipe $recipe2 */
    usort($recipes, fn($recipe1, $recipe2) => $recipe2->getScore() <=> $recipe1->getScore());

    return $recipes;
  }
}