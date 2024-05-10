<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502161646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5C7F3A37');
        $this->addSql('DROP INDEX IDX_C53D045F5C7F3A37 ON image');
        $this->addSql('ALTER TABLE image CHANGE figures_id figure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F5C011B5 ON image (figure_id)');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C5C7F3A37');
        $this->addSql('DROP INDEX IDX_7CC7DA2C5C7F3A37 ON video');
        $this->addSql('ALTER TABLE video CHANGE figures_id figure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C5C011B5 ON video (figure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5C011B5');
        $this->addSql('DROP INDEX IDX_C53D045F5C011B5 ON image');
        $this->addSql('ALTER TABLE image CHANGE figure_id figures_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5C7F3A37 FOREIGN KEY (figures_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C53D045F5C7F3A37 ON image (figures_id)');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C5C011B5');
        $this->addSql('DROP INDEX IDX_7CC7DA2C5C011B5 ON video');
        $this->addSql('ALTER TABLE video CHANGE figure_id figures_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C5C7F3A37 FOREIGN KEY (figures_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C5C7F3A37 ON video (figures_id)');
    }
}
