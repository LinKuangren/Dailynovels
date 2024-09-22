<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026213509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_novels (user_id INT NOT NULL, novels_id INT NOT NULL, INDEX IDX_FD00C68EA76ED395 (user_id), INDEX IDX_FD00C68E10AA0E6B (novels_id), PRIMARY KEY(user_id, novels_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_novels ADD CONSTRAINT FK_FD00C68EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_novels ADD CONSTRAINT FK_FD00C68E10AA0E6B FOREIGN KEY (novels_id) REFERENCES novels (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_novels DROP FOREIGN KEY FK_FD00C68EA76ED395');
        $this->addSql('ALTER TABLE user_novels DROP FOREIGN KEY FK_FD00C68E10AA0E6B');
        $this->addSql('DROP TABLE user_novels');
    }
}
