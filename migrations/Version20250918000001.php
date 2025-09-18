<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250918000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tablas de libros y reseñas';
    }

    public function up(Schema $schema): void
    {
        // Crear tabla books
        $this->addSql('CREATE TABLE books (
            id INT AUTO_INCREMENT NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            author VARCHAR(255) NOT NULL, 
            published_year INT NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Crear tabla reviews
        $this->addSql('CREATE TABLE reviews (
            id INT AUTO_INCREMENT NOT NULL, 
            book_id INT NOT NULL, 
            rating INT NOT NULL, 
            comment LONGTEXT NOT NULL, 
            created_at DATETIME NOT NULL, 
            INDEX IDX_6970EB0F16A2B381 (book_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Agregar foreign key
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F16A2B381 
            FOREIGN KEY (book_id) REFERENCES books (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F16A2B381');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE books');
    }
}