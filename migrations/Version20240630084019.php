<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240630084019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carousel (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carousel_product (carousel_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_B1E5D183C1CE5B98 (carousel_id), INDEX IDX_B1E5D1834584665A (product_id), PRIMARY KEY(carousel_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carousel_product ADD CONSTRAINT FK_B1E5D183C1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carousel_product ADD CONSTRAINT FK_B1E5D1834584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel_product DROP FOREIGN KEY FK_B1E5D183C1CE5B98');
        $this->addSql('ALTER TABLE carousel_product DROP FOREIGN KEY FK_B1E5D1834584665A');
        $this->addSql('DROP TABLE carousel');
        $this->addSql('DROP TABLE carousel_product');
    }
}
