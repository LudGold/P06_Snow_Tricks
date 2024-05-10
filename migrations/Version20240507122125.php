<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507122125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Renommer la colonne 'name' en 'imageName'
        $this->addSql('ALTER TABLE image CHANGE name imageName VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE image DROP image_name');
    }
    
    public function down(Schema $schema): void
    {
        // Revertir le renommage de la colonne 'imageName' en 'name'
        $this->addSql('ALTER TABLE image CHANGE imageName name VARCHAR(255) NOT NULL');
    }
    
}