<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240530100331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Recreate avatar table';
    }

    public function up(Schema $schema): void
    {
        // Check if the avatar table exists before creating it
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['avatar'])) {
            $this->addSql('CREATE TABLE avatar (
                id INT AUTO_INCREMENT NOT NULL,
                user_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                path VARCHAR(255) NOT NULL,
                image_url VARCHAR(255) DEFAULT NULL,
                UNIQUE INDEX UNIQ_1677722FA76ED395 (user_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            
            $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        }
    }

    public function down(Schema $schema): void
    {
        // Drop the avatar table if it exists
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['avatar'])) {
            $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
            $this->addSql('DROP TABLE avatar');
        }
    }
}
