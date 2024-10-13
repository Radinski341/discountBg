<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224152354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_order (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_choice_id INT DEFAULT NULL, cart_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_5475E8C44584665A (product_id), INDEX IDX_5475E8C4F726D05C (product_choice_id), INDEX IDX_5475E8C41AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C4F726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id)');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C41AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_product DROP FOREIGN KEY FK_2890CCAA1AD5CDBF');
        $this->addSql('ALTER TABLE cart_product DROP FOREIGN KEY FK_2890CCAA4584665A');
        $this->addSql('ALTER TABLE cart_product_choice DROP FOREIGN KEY FK_9E94F3F1AD5CDBF');
        $this->addSql('ALTER TABLE cart_product_choice DROP FOREIGN KEY FK_9E94F3FF726D05C');
        $this->addSql('DROP TABLE cart_product');
        $this->addSql('DROP TABLE cart_product_choice');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_product (cart_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2890CCAA4584665A (product_id), INDEX IDX_2890CCAA1AD5CDBF (cart_id), PRIMARY KEY(cart_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cart_product_choice (cart_id INT NOT NULL, product_choice_id INT NOT NULL, INDEX IDX_9E94F3FF726D05C (product_choice_id), INDEX IDX_9E94F3F1AD5CDBF (cart_id), PRIMARY KEY(cart_id, product_choice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAA1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_product_choice ADD CONSTRAINT FK_9E94F3F1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_product_choice ADD CONSTRAINT FK_9E94F3FF726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C44584665A');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C4F726D05C');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C41AD5CDBF');
        $this->addSql('DROP TABLE product_order');
    }
}
