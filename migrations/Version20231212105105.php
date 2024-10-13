<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212105105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, website_name VARCHAR(60) NOT NULL, website_url VARCHAR(100) NOT NULL, website_id VARCHAR(60) NOT NULL, product_url VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, old_price DOUBLE PRECISION NOT NULL, new_price DOUBLE PRECISION NOT NULL, original_discount_price DOUBLE PRECISION NOT NULL, original_discount_percent DOUBLE PRECISION NOT NULL, discount_percent DOUBLE PRECISION NOT NULL, images LONGTEXT NOT NULL, option_types VARCHAR(255) DEFAULT NULL, options VARCHAR(255) DEFAULT NULL, main_category VARCHAR(100) NOT NULL, sub_category VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product');
    }
}
