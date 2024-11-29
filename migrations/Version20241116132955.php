<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241116132955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE banner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE base_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE base_subcategory_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE carousel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE cart_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE favourite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE favourite_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE main_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "order_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_choice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sub_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_delivery_role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE banner (id INT NOT NULL, img VARCHAR(255) NOT NULL, url TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN banner.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE base_category (id INT NOT NULL, title VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, replace_category VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE base_subcategory (id INT NOT NULL, title VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, replace_subcategory VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE carousel (id INT NOT NULL, title VARCHAR(255) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE carousel_product (carousel_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(carousel_id, product_id))');
        $this->addSql('CREATE INDEX IDX_B1E5D183C1CE5B98 ON carousel_product (carousel_id)');
        $this->addSql('CREATE INDEX IDX_B1E5D1834584665A ON carousel_product (product_id)');
        $this->addSql('CREATE TABLE cart (id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, main_category_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, slug VARCHAR(255) NOT NULL, for_delete BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C1C6C55574 ON category (main_category_id)');
        $this->addSql('CREATE TABLE favourite (id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62A2CA19A76ED395 ON favourite (user_id)');
        $this->addSql('CREATE TABLE favourite_order (id INT NOT NULL, product_id INT NOT NULL, product_choice_id INT DEFAULT NULL, favourite_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_545C12ED4584665A ON favourite_order (product_id)');
        $this->addSql('CREATE INDEX IDX_545C12EDF726D05C ON favourite_order (product_choice_id)');
        $this->addSql('CREATE INDEX IDX_545C12ED7C7BA0AD ON favourite_order (favourite_id)');
        $this->addSql('CREATE TABLE main_category (id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, img VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "order" (id INT NOT NULL, user_id INT NOT NULL, taken_by_id INT DEFAULT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(30) NOT NULL, status_description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
        $this->addSql('CREATE INDEX IDX_F529939817F014F6 ON "order" (taken_by_id)');
        $this->addSql('CREATE TABLE order_transaction (id INT NOT NULL, product_id INT DEFAULT NULL, product_choice_id INT DEFAULT NULL, order_parent_id INT NOT NULL, original_website_url TEXT NOT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, status VARCHAR(30) NOT NULL, status_description TEXT DEFAULT NULL, product_image TEXT NOT NULL, product_title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_69857A574584665A ON order_transaction (product_id)');
        $this->addSql('CREATE INDEX IDX_69857A57F726D05C ON order_transaction (product_choice_id)');
        $this->addSql('CREATE INDEX IDX_69857A57CEFDB188 ON order_transaction (order_parent_id)');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, category_id INT NOT NULL, sub_category_id INT NOT NULL, website_name VARCHAR(60) NOT NULL, website_url VARCHAR(255) NOT NULL, website_id VARCHAR(255) NOT NULL, product_url TEXT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, old_price DOUBLE PRECISION NOT NULL, new_price DOUBLE PRECISION NOT NULL, original_discount_price DOUBLE PRECISION NOT NULL, original_discount_percent DOUBLE PRECISION NOT NULL, discount_percent DOUBLE PRECISION NOT NULL, images TEXT NOT NULL, option_types VARCHAR(255) DEFAULT NULL, options TEXT DEFAULT NULL, description TEXT DEFAULT NULL, for_delete BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, delivery_price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADF7BFE87C ON product (sub_category_id)');
        $this->addSql('CREATE TABLE product_choice (id INT NOT NULL, product_id INT NOT NULL, website_id VARCHAR(255) NOT NULL, option_type VARCHAR(255) NOT NULL, option_value VARCHAR(255) NOT NULL, title TEXT NOT NULL, old_price DOUBLE PRECISION NOT NULL, original_discount_price DOUBLE PRECISION NOT NULL, new_price DOUBLE PRECISION NOT NULL, images TEXT DEFAULT NULL, original_discount_percent DOUBLE PRECISION NOT NULL, discount_percent DOUBLE PRECISION NOT NULL, for_delete BOOLEAN NOT NULL, product_url TEXT NOT NULL, website_name VARCHAR(60) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3D71B364584665A ON product_choice (product_id)');
        $this->addSql('CREATE TABLE product_order (id INT NOT NULL, product_id INT NOT NULL, product_choice_id INT DEFAULT NULL, cart_id INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5475E8C44584665A ON product_order (product_id)');
        $this->addSql('CREATE INDEX IDX_5475E8C4F726D05C ON product_order (product_choice_id)');
        $this->addSql('CREATE INDEX IDX_5475E8C41AD5CDBF ON product_order (cart_id)');
        $this->addSql('CREATE TABLE sub_category (id INT NOT NULL, category_id INT NOT NULL, title VARCHAR(100) NOT NULL, slug VARCHAR(255) NOT NULL, for_delete BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BCE3F79812469DE2 ON sub_category (category_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone_number VARCHAR(20) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, populated_place VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, is_verifyed BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE website (id INT NOT NULL, website_name VARCHAR(60) NOT NULL, free_delivery_over DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE website_delivery_role (id INT NOT NULL, website_id INT NOT NULL, min DOUBLE PRECISION NOT NULL, max DOUBLE PRECISION NOT NULL, delivery_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8D560CF18F45C82 ON website_delivery_role (website_id)');
        $this->addSql('ALTER TABLE carousel_product ADD CONSTRAINT FK_B1E5D183C1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE carousel_product ADD CONSTRAINT FK_B1E5D1834584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1C6C55574 FOREIGN KEY (main_category_id) REFERENCES main_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favourite ADD CONSTRAINT FK_62A2CA19A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favourite_order ADD CONSTRAINT FK_545C12ED4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favourite_order ADD CONSTRAINT FK_545C12EDF726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favourite_order ADD CONSTRAINT FK_545C12ED7C7BA0AD FOREIGN KEY (favourite_id) REFERENCES favourite (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F529939817F014F6 FOREIGN KEY (taken_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_transaction ADD CONSTRAINT FK_69857A574584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_transaction ADD CONSTRAINT FK_69857A57F726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_transaction ADD CONSTRAINT FK_69857A57CEFDB188 FOREIGN KEY (order_parent_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_choice ADD CONSTRAINT FK_A3D71B364584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C44584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C4F726D05C FOREIGN KEY (product_choice_id) REFERENCES product_choice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C41AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F79812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_delivery_role ADD CONSTRAINT FK_A8D560CF18F45C82 FOREIGN KEY (website_id) REFERENCES website (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE banner_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE base_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE base_subcategory_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE carousel_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE cart_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE favourite_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE favourite_order_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE main_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "order_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE order_transaction_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_choice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_order_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sub_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_delivery_role_id_seq CASCADE');
        $this->addSql('ALTER TABLE carousel_product DROP CONSTRAINT FK_B1E5D183C1CE5B98');
        $this->addSql('ALTER TABLE carousel_product DROP CONSTRAINT FK_B1E5D1834584665A');
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1C6C55574');
        $this->addSql('ALTER TABLE favourite DROP CONSTRAINT FK_62A2CA19A76ED395');
        $this->addSql('ALTER TABLE favourite_order DROP CONSTRAINT FK_545C12ED4584665A');
        $this->addSql('ALTER TABLE favourite_order DROP CONSTRAINT FK_545C12EDF726D05C');
        $this->addSql('ALTER TABLE favourite_order DROP CONSTRAINT FK_545C12ED7C7BA0AD');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F529939817F014F6');
        $this->addSql('ALTER TABLE order_transaction DROP CONSTRAINT FK_69857A574584665A');
        $this->addSql('ALTER TABLE order_transaction DROP CONSTRAINT FK_69857A57F726D05C');
        $this->addSql('ALTER TABLE order_transaction DROP CONSTRAINT FK_69857A57CEFDB188');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADF7BFE87C');
        $this->addSql('ALTER TABLE product_choice DROP CONSTRAINT FK_A3D71B364584665A');
        $this->addSql('ALTER TABLE product_order DROP CONSTRAINT FK_5475E8C44584665A');
        $this->addSql('ALTER TABLE product_order DROP CONSTRAINT FK_5475E8C4F726D05C');
        $this->addSql('ALTER TABLE product_order DROP CONSTRAINT FK_5475E8C41AD5CDBF');
        $this->addSql('ALTER TABLE sub_category DROP CONSTRAINT FK_BCE3F79812469DE2');
        $this->addSql('ALTER TABLE website_delivery_role DROP CONSTRAINT FK_A8D560CF18F45C82');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE base_category');
        $this->addSql('DROP TABLE base_subcategory');
        $this->addSql('DROP TABLE carousel');
        $this->addSql('DROP TABLE carousel_product');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE favourite');
        $this->addSql('DROP TABLE favourite_order');
        $this->addSql('DROP TABLE main_category');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_transaction');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_choice');
        $this->addSql('DROP TABLE product_order');
        $this->addSql('DROP TABLE sub_category');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE website');
        $this->addSql('DROP TABLE website_delivery_role');
    }
}
