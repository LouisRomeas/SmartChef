<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTime;
use DateTimeInterface;

class TrendingContainer {
  public function __construct(private RecipeRepository $recipeRepository) {}

  /**
   * Retrieve "trending" recipes from a set of arbitrary rules
   * 
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

    /**
     * Sort recipes first by score, then by views
     * @param Recipe $recipe1
     * @param Recipe $recipe2
     * */
    usort($recipes, function($recipe1, $recipe2) {
      $scoreComparison = $recipe2->getScore() <=> $recipe1->getScore();
      if ($scoreComparison !== 0) return $scoreComparison;

      $viewsComparison = $recipe2->getViews() <=> $recipe1->getViews();
      if ($viewsComparison !== 0) return $viewsComparison;

      return 0;
    });

    return $recipes;
  }
}