<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507125528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove name and image_name columns and add imageName column to image table';
    }

    public function up(Schema $schema): void
    {
        // Check if the column exists before attempting to drop it
        if ($schema->getTable('image')->hasColumn('name')) {
            $this->addSql('ALTER TABLE image DROP name');
        }

        // Check if the column exists before attempting to drop it
        if ($schema->getTable('image')->hasColumn('image_name')) {
            $this->addSql('ALTER TABLE image DROP image_name');
        }

        // Add the new imageName column
        $this->addSql('ALTER TABLE image ADD imageName VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Check if the column exists before attempting to drop it
        if (!$schema->getTable('image')->hasColumn('name')) {
            $this->addSql('ALTER TABLE image ADD name VARCHAR(255) NOT NULL');
        }

        // Check if the column exists before attempting to drop it
        if (!$schema->getTable('image')->hasColumn('image_name')) {
            $this->addSql('ALTER TABLE image ADD image_name VARCHAR(255) NOT NULL');
        }

        // Remove the imageName column
        $this->addSql('ALTER TABLE image DROP imageName');
    }
}
