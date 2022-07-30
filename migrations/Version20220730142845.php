<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220730142845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD default_emoji VARCHAR(15) NOT NULL DEFAULT "🍴"');

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
        $this->addSql('ALTER TABLE category DROP default_emoji');
    }
}
