<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808214228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE novels DROP FOREIGN KEY FK_9852DDB3D1DE6B2B');
        $this->addSql('DROP INDEX IDX_9852DDB3D1DE6B2B ON novels');
        $this->addSql('ALTER TABLE novels CHANGE traducteur_id_id traducteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE novels ADD CONSTRAINT FK_9852DDB33954F290 FOREIGN KEY (traducteur_id) REFERENCES traducteurs (id)');
        $this->addSql('CREATE INDEX IDX_9852DDB33954F290 ON novels (traducteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE novels DROP FOREIGN KEY FK_9852DDB33954F290');
        $this->addSql('DROP INDEX IDX_9852DDB33954F290 ON novels');
        $this->addSql('ALTER TABLE novels CHANGE traducteur_id traducteur_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE novels ADD CONSTRAINT FK_9852DDB3D1DE6B2B FOREIGN KEY (traducteur_id_id) REFERENCES traducteurs (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9852DDB3D1DE6B2B ON novels (traducteur_id_id)');
    }
}
