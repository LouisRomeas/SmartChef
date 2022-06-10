<?php

namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IngredientTransformer implements DataTransformerInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
      
    }

    /**
     * Transforms an object (ingredient) to a string (number).
     *
     * @param  Ingredient|null $ingredient
     */
    public function transform($ingredient): string
    {
        if (null === $ingredient) {
            return '';
        }

        return $ingredient->getId();
    }

    /**
     * Transforms a string (number) to an object (ingredient).
     *
     * @param  string $ingredientNumber
     * @throws TransformationFailedException if object (ingredient) is not found.
     */
    public function reverseTransform($ingredientNumber): ?Ingredient
    {
        // no ingredient number? It's optional, so that's ok
        if (!$ingredientNumber) {
            return null;
        }

        $ingredient = $this->entityManager
            ->getRepository(Ingredient::class)
            // query for the ingredient with this id
            ->find($ingredientNumber)
        ;

        if (null === $ingredient) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An ingredient with number "%s" does not exist!',
                $ingredientNumber
            ));
        }

        return $ingredient;
    }
}