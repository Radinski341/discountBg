<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128172621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favourite (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_62A2CA19A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favourite_order (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_choice_id INT DEFAULT NULL, favourite_id INT NOT NULL, INDEX IDX_545C12ED4584665A (product_id), INDEX IDX_545C12EDF726D05C (product_choice_id), INDEX IDX_545C12ED7C7BA0AD (favourite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favourite ADD CONSTRAINT FK_62A2CA19A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favourite_order ADD CONSTRAINT FK_545C12ED4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE favourite_order ADD CONSTRAINT FK_545C12EDF726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id)');
        $this->addSql('ALTER TABLE favourite_order ADD CONSTRAINT FK_545C12ED7C7BA0AD FOREIGN KEY (favourite_id) REFERENCES favourite (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favourite DROP FOREIGN KEY FK_62A2CA19A76ED395');
        $this->addSql('ALTER TABLE favourite_order DROP FOREIGN KEY FK_545C12ED4584665A');
        $this->addSql('ALTER TABLE favourite_order DROP FOREIGN KEY FK_545C12EDF726D05C');
        $this->addSql('ALTER TABLE favourite_order DROP FOREIGN KEY FK_545C12ED7C7BA0AD');
        $this->addSql('DROP TABLE favourite');
        $this->addSql('DROP TABLE favourite_order');
    }
}
