<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Report;
use App\Entity\Category;
use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class StatsContainer {
  public function __construct(private EntityManagerInterface $entityManager) {}

  /**
   * Retrieve counts of multiple entities, for use in displaying website's stats
   */
  public function getStats(): array {
    $entities = [
      User::class,
      Recipe::class,
      Report::class,
      Ingredient::class,
      Category::class
    ];

    $stats = [];

    foreach ($entities as $entity) {
      /** @var ServiceEntityRepository $repo */
      $repo = $this->entityManager->getRepository($entity);

      $entityTranslatableName =  'entity.' . lcfirst( preg_replace("/.*\\\([[:alpha:]]+)$/", "$1", $entity) ) . '._plural';

      $stats[$entityTranslatableName] = $repo->createQueryBuilder('entity')
                      ->select('count(entity.id)')
                      ->getQuery()
                      ->getSingleScalarResult()
                      ;
        
    }

    return $stats;
  }
}