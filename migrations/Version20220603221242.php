<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603221242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, unit_id INT NOT NULL, name VARCHAR(255) NOT NULL, emoji VARCHAR(15) DEFAULT NULL, INDEX IDX_6BAF787012469DE2 (category_id), INDEX IDX_6BAF7870F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, body VARCHAR(10000) NOT NULL, duration TIME NOT NULL, INDEX IDX_DA88B137F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_ingredient (id INT AUTO_INCREMENT NOT NULL, ingredient_id INT NOT NULL, recipe_id INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, is_optional TINYINT(1) NOT NULL, INDEX IDX_22D1FE13933FE08C (ingredient_id), INDEX IDX_22D1FE1359D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, reason_id INT NOT NULL, user_id INT NOT NULL, recipe_id INT NOT NULL, body VARCHAR(2500) DEFAULT NULL, INDEX IDX_C42F778459BB1592 (reason_id), INDEX IDX_C42F7784A76ED395 (user_id), INDEX IDX_C42F778459D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_reason (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nickname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT NOT NULL, is_positive TINYINT(1) NOT NULL, INDEX IDX_5A108564A76ED395 (user_id), INDEX IDX_5A10856459D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF787012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE13933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE1359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778459BB1592 FOREIGN KEY (reason_id) REFERENCES report_reason (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778459D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856459D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');

        // CUSTOM VALUES
        $this->addSql("INSERT INTO `unit` (`id`, `name`) VALUES
            (1, '×'),
            (2, 'g'),
            (3, 'mL')
        ");

        $this->addSql("INSERT INTO `report_reason` (`id`, `name`) VALUES
            (1, 'Erreur dans la recette'),
            (2, 'Contenu inapproprié')
        ");
        
        $this->addSql("INSERT INTO `category` (`id`, `name`, `default_emoji`) VALUES
            (1, 'Œufs et Produits latiers', '🥛'),
            (2, 'Légumes', '🌱'),
            (3, 'Épices, herbes et graines', '🍃'),
            (4, 'Sucre et édulcorants', '🍯'),
            (5, 'Fruits', '🍎'),
            (6, 'Farines et levures', '🍰'),
            (7, 'Huiles et graisses', '🏺'),
            (8, 'Fromages', '🧀'),
            (9, 'Sauces, pickles et condiments', '🧂'),
            (10, 'Fruits séchés, en conserve et confits', '🥫'),
            (11, 'Poissons et fruits de mer', '🐟'),
            (12, 'Chocolat et encas sucrés', '🍫'),
            (13, 'Alcool', '🍷'),
            (14, 'Champignons et légumineuses', '🍄'),
            (15, 'Charcuterie et rillettes', '🥓'),
            (16, 'Pains, boulangerie et snacks', '🥖'),
            (17, 'Fonds, fumets, soupes et bouillons', '🍲'),
            (18, 'Volaille', '🍗'),
            (19, 'Café, thé et boissons sans alcool', '🍵'),
            (20, 'Viandes', '🥩'),
            (21, 'Pâte réfrigérée', '🍕'),
            (22, 'Confiserie', '🍬'),
            (23, 'Céréales', '🌾'),
            (24, 'Pâtes alimentaires', '🍝'),
            (25, 'Produits végétariens et végan', '🌿'),
            (26, 'Fleurs comestibles', '🍴')
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF787012469DE2');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE13933FE08C');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE1359D8A214');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778459D8A214');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856459D8A214');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778459BB1592');
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF7870F8BD700D');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137F675F31B');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A76ED395');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE report_reason');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vote');
    }
}
