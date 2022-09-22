<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function add(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * This is the core method of this project, as it is at the heart of the
     * customizable search process.
     * 
     * Retrieve all recipes that use only given ingredients (from non-optional ingredients),
     * in the given quantity, proportionally adjusted for the number of portions asked
     * 
     * @param RecipeIngredient[] $recipeIngredients
     * 
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function getExactMatches(array $recipeIngredients, int $portions, int $limit = 10): array
    {
        // Add all ingredients to a list of "allowed ingredients"
        $allowedIngredients = [];
        foreach ($recipeIngredients as $recipeIngredient) {
            $allowedIngredients[] = $recipeIngredient->getIngredient();
        }

        /**
         * Start building a DQL query that only selects recipes which do not
         * contain any ingredient *not* on the "allowed ingredients" list
         */
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder
            ->join('r.recipeIngredients', 'ri')
            ->groupBy('r')
            ->andHaving('
            SUM(
                IFELSE(
                    (
                        ri.ingredient NOT IN(:allowedIngredients) AND
                        ri.isOptional = 0
                    ),
                    1,
                    0
                )
            ) = 0
            ')
        ;

        $quantityExpression = "SUM( IFELSE(";
        $index = 0;
        /**
         * Add a custom written DQL expression
         * Loop through every RecipeIngredient in the search, each time add a condition for
         * quantity, considering the ratio between the search's portions & the recipe's portions
         */
        foreach ($recipeIngredients as $recipeIngredient) {
            $index++;

            $quantityExpression .= "(
                ri.ingredient = :ingredient_$index AND (ri.quantity / r.portions) > (:quantity_$index / :portions)
            )";
            $quantityExpression .= array_key_last($recipeIngredients) != $index ?' OR ' : ', ';

            $queryBuilder->setParameter("ingredient_$index", $recipeIngredient->getIngredient());
            $queryBuilder->setParameter("quantity_$index", $recipeIngredient->getQuantity());
        }
        $quantityExpression .= '1, 0)) = 0';
        $queryBuilder->setParameter('portions', $portions);

        $queryBuilder->andHaving($quantityExpression);


        $queryBuilder
            ->setParameter('allowedIngredients', $allowedIngredients)
            ->orderBy('r.views', 'DESC')
            ->setMaxResults($limit)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Retrieve all recipes more recent than a given DateTime,
     * ordered by date of creation (ascending or descending)
     * 
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function findRecent(
        DateTimeInterface $earliest,
        $limit = null,
        $offset = null,
        bool $ascending = false
    ): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.createdAt >= :earliest')
            ->setParameter('earliest', $earliest)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy(
                'r.createdAt',
                ( $ascending ? 'asc' : 'desc' )
            )
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Recipe[] Returns an array of Recipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recipe
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
