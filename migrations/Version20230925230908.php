<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925230908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE novels_chapitres DROP FOREIGN KEY FK_56B7825510AA0E6B');
        $this->addSql('ALTER TABLE novels_chapitres DROP FOREIGN KEY FK_56B7825520B9AB7E');
        $this->addSql('DROP TABLE novels_chapitres');
        $this->addSql('ALTER TABLE annonces CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE chapitres ADD novels_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chapitres ADD CONSTRAINT FK_508679FC10AA0E6B FOREIGN KEY (novels_id) REFERENCES novels (id)');
        $this->addSql('CREATE INDEX IDX_508679FC10AA0E6B ON chapitres (novels_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE novels_chapitres (novels_id INT NOT NULL, chapitres_id INT NOT NULL, INDEX IDX_56B7825510AA0E6B (novels_id), INDEX IDX_56B7825520B9AB7E (chapitres_id), PRIMARY KEY(novels_id, chapitres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE novels_chapitres ADD CONSTRAINT FK_56B7825510AA0E6B FOREIGN KEY (novels_id) REFERENCES novels (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE novels_chapitres ADD CONSTRAINT FK_56B7825520B9AB7E FOREIGN KEY (chapitres_id) REFERENCES chapitres (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonces CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE chapitres DROP FOREIGN KEY FK_508679FC10AA0E6B');
        $this->addSql('DROP INDEX IDX_508679FC10AA0E6B ON chapitres');
        $this->addSql('ALTER TABLE chapitres DROP novels_id');
    }
}
