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
     * @param RecipeIngredient[] $recipeIngredients
     * 
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function getExactMatches(array $recipeIngredients, int $portions): array
    {
        $allowedIngredients = [];
        foreach ($recipeIngredients as $recipeIngredient) {
            $allowedIngredients[] = $recipeIngredient->getIngredient();
        }

        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder
            ->join('r.recipeIngredients', 'ri')
            ->groupBy('r')
            ->andHaving('
            SUM(
                IFELSE(
                    ri.ingredient NOT IN(:allowedIngredients),
                    1,
                    0
                )
            ) = 0
            ')
        ;

        // Loop through every RecipeIngredient searched, and add a condition for quantity
        $quantityExpression = "SUM( IFELSE(";
        foreach ($recipeIngredients as $index => $recipeIngredient) {
            // Sanitizing just in case
            $index = intval($index);

            $quantityExpression .= "(
                ri.ingredient = :ingredient_$index AND (ri.quantity / r.portions) > (:quantity_$index / :portions)
            )";
            $quantityExpression .= array_key_last($recipeIngredients) !== $index ?' OR ' : ', ';

            $queryBuilder->setParameter("ingredient_$index", $recipeIngredient->getIngredient());
            $queryBuilder->setParameter("quantity_$index", $recipeIngredient->getQuantity());
        }
        $quantityExpression .= '1, 0)) = 0';
        $queryBuilder->setParameter('portions', $portions);

        $queryBuilder->andHaving($quantityExpression);


        $queryBuilder
            ->setParameter('allowedIngredients', $allowedIngredients)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function findRecent(
        DateTimeInterface $earliest,
        $limit = null,
        $offset = null
    ): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.createdAt >= :earliest')
            ->setParameter('earliest', $earliest)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
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
