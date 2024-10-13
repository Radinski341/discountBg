<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204095919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, taken_by_id INT DEFAULT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(30) NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F529939817F014F6 (taken_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_transaction (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, product_choice_id INT DEFAULT NULL, order_parent_id INT NOT NULL, original_website_url LONGTEXT NOT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_69857A574584665A (product_id), INDEX IDX_69857A57F726D05C (product_choice_id), INDEX IDX_69857A57CEFDB188 (order_parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939817F014F6 FOREIGN KEY (taken_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_transaction ADD CONSTRAINT FK_69857A574584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_transaction ADD CONSTRAINT FK_69857A57F726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id)');
        $this->addSql('ALTER TABLE order_transaction ADD CONSTRAINT FK_69857A57CEFDB188 FOREIGN KEY (order_parent_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939817F014F6');
        $this->addSql('ALTER TABLE order_transaction DROP FOREIGN KEY FK_69857A574584665A');
        $this->addSql('ALTER TABLE order_transaction DROP FOREIGN KEY FK_69857A57F726D05C');
        $this->addSql('ALTER TABLE order_transaction DROP FOREIGN KEY FK_69857A57CEFDB188');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_transaction');
    }
}
