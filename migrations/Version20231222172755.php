<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222172755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_choice (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, website_id VARCHAR(255) NOT NULL, option_type VARCHAR(255) NOT NULL, option_value VARCHAR(255) NOT NULL, title LONGTEXT NOT NULL, old_price DOUBLE PRECISION NOT NULL, original_discount_price DOUBLE PRECISION NOT NULL, new_price DOUBLE PRECISION NOT NULL, images LONGTEXT DEFAULT NULL, original_discount_percent DOUBLE PRECISION NOT NULL, discount_percent DOUBLE PRECISION NOT NULL, INDEX IDX_A3D71B364584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_choice ADD CONSTRAINT FK_A3D71B364584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_choice DROP FOREIGN KEY FK_A3D71B364584665A');
        $this->addSql('DROP TABLE product_choice');
    }
}
