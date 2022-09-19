<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Unit;
use Faker\Generator;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\ReportReason;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $objectManager;
    private Generator $faker;

    private array $units;
    private array $reportReasons;
    private array $categories;
    private array $ingredients;
    private array $users;

    const NB_CATEGORIES = 10;
    const NB_INGREDIENTS = 2500;
    const NB_USERS = 250;

    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
        $this->faker = Factory::create();
        
        $this->units = $this->addUnits();
        $this->reportReasons = $this->addReportReasons();
        $this->categories = $this->addCategories();
        $this->ingredients = $this->addIngredients();
        $this->users = $this->addUsers();

        $objectManager->flush();
    }

    private function getRandomEntity(array $entities): object {
        return $entities[random_int(0, count($entities) - 1)];
    }

    /**
     * @return Unit[]
     */
    private function addUnits() {
        $associated = [
            [ 'name' => '×' ],
            [ 'name' => 'g' ],
            [ 'name' => 'mL' ]
        ];

        $units = [];

        foreach ($associated as $mappedUnit) {
            $unit = new Unit();
            $unit->setName($mappedUnit['name']);
            $this->objectManager->persist($unit);

            $units[] = $unit;
        }

        return $units;
    }

    /**
     * @return ReportReason[]
     */
    private function addReportReasons() {

        $reportReasons = [];

        for ($i=0; $i < 10; $i++) {
            $reportReason = new ReportReason();
            $reportReason->setName( $this->faker->sentence(random_int(2, 6)) );
            $this->objectManager->persist($reportReason);

            $reportReasons[] = $reportReason;
        }

        return $reportReasons;
    }

    /**
     * @return Category[]
     */
    private function addCategories() {

        $categories = [];

        for ($i=0; $i < self::NB_CATEGORIES; $i++) {
            $category = new Category();
            $category->setName( ucfirst( $this->faker->words(random_int(1, 3), true) ) );
            $category->setDefaultEmoji($this->faker->emoji());
            $this->objectManager->persist($category);

            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * @return Ingredient[]
     */
    private function addIngredients() {

        $ingredients = [];

        for ($i=0; $i < self::NB_INGREDIENTS; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName( ucfirst( $this->faker->words(random_int(1, 2), true) ) );
            $ingredient->setEmoji($this->faker->emoji());
            $ingredient->setCategory($this->getRandomEntity($this->categories));
            $ingredient->setUnit($this->getRandomEntity($this->units));
            $this->objectManager->persist($ingredient);

            $ingredients[] = $ingredient;
        }

        return $ingredients;
    }

    /**
     * @return User[]
     */
    private function addUsers() {

        $users = [];

        for ($i=0; $i < self::NB_USERS; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            $user->setNickname(
                $this->faker->firstName() . (
                    random_int(1, 3) >= 2 ?
                    ' ' . $this->faker->lastName() :
                    ''
                ) . ' ' . $this->faker->randomNumber(5, true)
            );

            $user->setPassword(
                $this->hasher->hashPassword($user, $this->faker->password())
            );
            
            $this->objectManager->persist($user);

            $users[] = $user;
        }

        return $users;
    }
}
