<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412121416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, figure_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64C19C16D69186E (figure_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C16D69186E FOREIGN KEY (figure_id_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE figure ADD categories_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA21214B7 FOREIGN KEY (categories_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AA21214B7 ON figure (categories_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA21214B7');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C16D69186E');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP INDEX IDX_2F57B37AA21214B7 ON figure');
        $this->addSql('ALTER TABLE figure DROP categories_id');
    }
}
