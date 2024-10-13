<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920073519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE website_delivery_role (id INT AUTO_INCREMENT NOT NULL, website_id INT NOT NULL, min DOUBLE PRECISION NOT NULL, max DOUBLE PRECISION NOT NULL, delivery_price DOUBLE PRECISION NOT NULL, INDEX IDX_A8D560CF18F45C82 (website_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE website_delivery_role ADD CONSTRAINT FK_A8D560CF18F45C82 FOREIGN KEY (website_id) REFERENCES website (id)');
        $this->addSql('ALTER TABLE website ADD free_delivery_over DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE website_delivery_role DROP FOREIGN KEY FK_A8D560CF18F45C82');
        $this->addSql('DROP TABLE website_delivery_role');
        $this->addSql('ALTER TABLE website DROP free_delivery_over');
    }
}
