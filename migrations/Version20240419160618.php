<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419160618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD name VARCHAR(255) DEFAULT "Kire", ADD last_name VARCHAR(255) DEFAULT "Radinski", ADD phone_number VARCHAR(20) DEFAULT "+359885123411", ADD address VARCHAR(255) DEFAULT "Malinova Dolina blok 8", ADD city VARCHAR(255) DEFAULT "Sofia", ADD populated_place VARCHAR(255) DEFAULT "test"');
        $this->addSql('ALTER TABLE user CHANGE name name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL, CHANGE phone_number phone_number VARCHAR(20) NOT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE city city VARCHAR(255) NOT NULL, CHANGE populated_place populated_place VARCHAR(255) NOT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP name, DROP last_name, DROP phone_number, DROP address, DROP city, DROP populated_place');
    }
}
