<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Vote;
use Faker\Generator;
use App\Entity\Recipe;
use DateTimeImmutable;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\ReportReason;
use App\Entity\RecipeIngredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * These Fixtures are a little extra I created in order to simulate what this
 * website would look like if it had a lot more activity.
 */
class AppFixtures extends Fixture
{
    private ObjectManager $objectManager;
    private Generator $faker;
    private int $startTime;

    private array $units;
    private array $reportReasons;
    private array $categories;
    private array $ingredients;
    private array $users;
    private array $realUsers;
    private array $recipes;
    private array $votes;

    const FOOD_EMOJIS = [
        "🍏",
        "🍎",
        "🍐",
        "🍊",
        "🍋",
        "🍌",
        "🍉",
        "🍇",
        "🍓",
        "🍈",
        "🍒",
        "🍑",
        "🍍",
        "🍅",
        "🍆",
        "🌽",
        "🍠",
        "🍯",
        "🍞",
        "🧀",
        "🍗",
        "🍖",
        "🍤",
        "🍳",
        "🍔",
        "🍟",
        "🌭",
        "🍕",
        "🍝",
        "🌮",
        "🌯",
        "🍜",
        "🍲",
        "🍥",
        "🍣",
        "🍱",
        "🍛",
        "🍙",
        "🍚",
        "🍘",
        "🍢",
        "🍡",
        "🍧",
        "🍨",
        "🍦",
        "🍰",
        "🎂",
        "🍮",
        "🍬",
        "🍭",
        "🍫",
        "🍿",
        "🍩",
        "🍪",
        "🍺",
        "🍻",
        "🍷",
        "🍸",
        "🍹",
        "🍾",
        "🍶",
        "🍵",
        "☕️",
        "🍼"
    ];
    const NB_CATEGORIES = 10;
    const NB_INGREDIENTS = 2500;
    const NB_USERS = 50;
    const RATIO_EDITORS = 1/3;
    const NB_RECIPES = 35;
    const RANGE_RECIPE_INGREDIENTS = [
        'min' => 1,
        'max' => 7
    ];
    const NB_RECIPES_VOTED = 35;
    const NB_VOTES = 10000;

    public function __construct(private UserPasswordHasherInterface $hasher, private SluggerInterface $slugger) {}

    public function load(ObjectManager $objectManager): void
    {
        ini_set('memory_limit', '4G');

        $this->startTime = hrtime(true);
        $this->echoEvent('START');

        $this->objectManager = $objectManager;
        $this->faker = Factory::create();
        $this->echoEvent('FAKER FACTORY');

        $this->units = $this->addUnits();
        $this->echoEvent('Units');

        $this->reportReasons = $this->addReportReasons();
        $this->echoEvent('ReportReasons');

        $this->categories = $this->addCategories();
        $this->echoEvent('Categories');

        $this->ingredients = $this->addIngredients();
        $this->echoEvent('Ingredients');

        $this->users = $this->addUsers();
        $this->echoEvent('Users (fake)');

        $this->realUsers = $this->addRealUsers();
        $this->echoEvent('Users (real)');

        $this->recipes = $this->addRecipes();
        $this->echoEvent('Recipes');

        $this->votes = $this->addVotes();
        $this->echoEvent('Votes');

        $objectManager->flush();
        
        $this->echoEvent('FLUSH');
    }

    private function getRandomEntity(array $entities): object {
        return $entities[random_int(0, count($entities) - 1)];
    }

    private function echoEvent(string $event = ''): int {
        $now = hrtime(true);
        echo "$event: " . round( ($now - $this->startTime) / 1e+6, 3 ) . 'ms' . PHP_EOL;

        return $now;
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
            $category->setDefaultEmoji( self::FOOD_EMOJIS[ array_rand( self::FOOD_EMOJIS ) ] );
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
            $ingredient->setEmoji( self::FOOD_EMOJIS[ array_rand( self::FOOD_EMOJIS ) ] );
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
            $user->setEmail( $this->faker->randomNumber(3) . $this->faker->email() );
            $user->setNickname(
                $this->faker->firstName() . (
                    random_int(1, 3) >= 2 ?
                    ' ' . $this->faker->lastName() :
                    ''
                ) . ' ' . $this->faker->randomNumber(3, true)
            );

            $user->setPassword(
                '{{hashed_password}}'
            );

            $user->setIsVerified(true);

            if ( rand(0, 100) < self::RATIO_EDITORS * 100 || $i == 0 ) $user->setRoles(['ROLE_EDITOR']);
            
            $this->objectManager->persist($user);

            $users[] = $user;
        }

        return $users;
    }

    /**
     * @return User[]
     */
    private function addRealUsers() {

        $users = [];

        // Regular user
        $userRegular = new User();
        $userRegular->setEmail("user@test.test");
        $userRegular->setNickname("User 123");
        $userRegular->setIsVerified(true);

        $userRegular->setPassword(
            $this->hasher->hashPassword($userRegular, 'user123')
        );
        
        $this->objectManager->persist($userRegular);

        $users[] = $userRegular;


        // Admin user
        $userAdmin = new User();
        $userAdmin->setEmail("admin@test.test");
        $userAdmin->setNickname("Admin 123");
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setIsVerified(true);

        $userAdmin->setPassword(
            $this->hasher->hashPassword($userAdmin, 'admin123')
        );
        
        $this->objectManager->persist($userAdmin);

        $users[] = $userAdmin;

        
        // Unverified user
        $userUnverified = new User();
        $userUnverified->setEmail("unverified@test.test");
        $userUnverified->setNickname("Unverified 123");
        $userUnverified->setIsVerified(false);

        $userUnverified->setPassword(
            $this->hasher->hashPassword($userUnverified, 'unverified123')
        );
        
        $this->objectManager->persist($userUnverified);

        $users[] = $userUnverified;


        return $users;
    }

    /**
     * @return Recipe[]
     */
    private function addRecipes() {

        $recipes = [];

        for ($i=0; $i < self::NB_RECIPES; $i++) {
            echo "Recipe $i/" . self::NB_RECIPES . PHP_EOL;
            $recipe = new Recipe();
            $recipe->setTitle( ucfirst( $this->faker->words(random_int(1, 4), true) ) );
            $recipe->setBody( nl2br( $this->faker->paragraphs(rand(4, 10), true) ) );
            $recipe->setDuration(
                new DateTime(
                    max( rand(0, 6) - 3, 0 ) . ':' . rand(0, 59) . ':' . rand(0, 59)
                )
            );
            $recipe->setPortions($this->faker->numberBetween(1, 10));
            $recipe->setCreatedAt(DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
            $recipe->setViews( $this->faker->numberBetween(500, 1500000) );
            $recipe->setSlug( $this->slugger->slug( $this->faker->randomNumber(3) . ' ' . strtolower( $recipe->getTitle() ) ) );

            do {
                /** @var User $author */
                $author = $this->getRandomEntity($this->users);
            } while (
                in_array(
                    'ROLE_EDITOR',
                    $author->getRoles()
                )
            );

            $recipe->setAuthor($author);

            // RecipeIngredients
            $ingredientsNb = rand(self::RANGE_RECIPE_INGREDIENTS['min'], self::RANGE_RECIPE_INGREDIENTS['max']);

            for ($j = 0; $j < $ingredientsNb; $j++) {
                $recipeIngredient = new RecipeIngredient();
                $recipeIngredient->setQuantity( $this->faker->randomNumber( rand(1, 3) ) );
                $recipeIngredient->setIsOptional(
                    rand(0, $ingredientsNb - 1) > 1 && rand(0, 3) == 0
                );

                $recipeIngredient->setIngredient($this->getRandomEntity($this->ingredients));
                $recipeIngredient->setRecipe($recipe);
                
                $this->objectManager->persist($recipeIngredient);
            }

            $this->objectManager->persist($recipe);

            $recipes[] = $recipe;
        }

        return $recipes;
    }

    /**
     * @return Vote[]
     */
    private function addVotes() {

        $votes = [];

        for ($i=0; $i < self::NB_VOTES; $i++) {
            if ( !($i % 100) ) gc_collect_cycles();
            echo "Vote $i/" . self::NB_VOTES . PHP_EOL;

            $recipe = $this->recipes[ count($this->recipes) - 1 - rand(0, self::NB_RECIPES_VOTED - 1) ];
            
            $vote = new Vote();
            $vote->setRecipe($recipe);
            $vote->setUser( $this->getRandomEntity($this->users) );
            $vote->setIsPositive(
                rand(1, 4) > 1
            );

            $this->objectManager->persist($vote);

            $votes[] = $vote;
        }

        return $votes;
    }

}
