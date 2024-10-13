<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231226083801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('DROP INDEX UNIQ_BA388B7A76ED395 ON cart');
        $this->addSql('ALTER TABLE cart DROP user_id');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C41AD5CDBF');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C44584665A');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C4F726D05C');
        $this->addSql('DROP INDEX IDX_5475E8C4F726D05C ON product_order');
        $this->addSql('DROP INDEX IDX_5475E8C44584665A ON product_order');
        $this->addSql('DROP INDEX IDX_5475E8C41AD5CDBF ON product_order');
        $this->addSql('ALTER TABLE product_order DROP product_id, DROP product_choice_id, DROP cart_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_order ADD product_id INT NOT NULL, ADD product_choice_id INT DEFAULT NULL, ADD cart_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C41AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C4F726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5475E8C4F726D05C ON product_order (product_choice_id)');
        $this->addSql('CREATE INDEX IDX_5475E8C44584665A ON product_order (product_id)');
        $this->addSql('CREATE INDEX IDX_5475E8C41AD5CDBF ON product_order (cart_id)');
        $this->addSql('ALTER TABLE cart ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA388B7A76ED395 ON cart (user_id)');
    }
}
