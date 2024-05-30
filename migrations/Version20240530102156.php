<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530102156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FFA40957A');
        $this->addSql('DROP INDEX UNIQ_1677722FFA40957A ON avatar');
        $this->addSql('ALTER TABLE avatar ADD user_id INT NOT NULL, DROP avatars_id, DROP avatar, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1677722FA76ED395 ON avatar (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
        $this->addSql('DROP INDEX UNIQ_1677722FA76ED395 ON avatar');
        $this->addSql('ALTER TABLE avatar ADD avatars_id INT DEFAULT NULL, ADD avatar VARCHAR(255) NOT NULL, DROP user_id, CHANGE image_url image_url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FFA40957A FOREIGN KEY (avatars_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1677722FFA40957A ON avatar (avatars_id)');
    }
}
