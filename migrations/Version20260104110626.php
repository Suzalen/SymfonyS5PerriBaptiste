<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104110626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, firstname, lastname, email, company FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, address CLOB DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO client (id, firstname, lastname, email, company) SELECT id, firstname, lastname, email, company FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455E7927C74 ON client (email)');
        $this->addSql('ALTER TABLE product ADD COLUMN type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD COLUMN weight DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD COLUMN dimensions VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD COLUMN stock INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD COLUMN license_details VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, firstname, lastname, email, company FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO client (id, firstname, lastname, email, company) SELECT id, firstname, lastname, email, company FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO product (id, name, description, price) SELECT id, name, description, price FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }
}
