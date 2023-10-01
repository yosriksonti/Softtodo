<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930112801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Project (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updatedAt DATETIME NOT NULL, title VARCHAR(191) NOT NULL, image VARCHAR(255) NOT NULL, filename VARCHAR(191) DEFAULT NULL, nbTasks INT NOT NULL, description VARCHAR(5000) NOT NULL, status VARCHAR(5000) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updatedAt DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Project');
        $this->addSql('ALTER TABLE user DROP createdAt, DROP updatedAt');
    }
}
