<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808214008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapitres (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE novels (id INT AUTO_INCREMENT NOT NULL, traducteur_id_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, illustration VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, visibilitie TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9852DDB3D1DE6B2B (traducteur_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE novels_categories (novels_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_95FF6CDF10AA0E6B (novels_id), INDEX IDX_95FF6CDFA21214B7 (categories_id), PRIMARY KEY(novels_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE novels_tags (novels_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_67600D7110AA0E6B (novels_id), INDEX IDX_67600D718D7B4FB4 (tags_id), PRIMARY KEY(novels_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE novels_chapitres (novels_id INT NOT NULL, chapitres_id INT NOT NULL, INDEX IDX_56B7825510AA0E6B (novels_id), INDEX IDX_56B7825520B9AB7E (chapitres_id), PRIMARY KEY(novels_id, chapitres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE traducteurs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE novels ADD CONSTRAINT FK_9852DDB3D1DE6B2B FOREIGN KEY (traducteur_id_id) REFERENCES traducteurs (id)');
        $this->addSql('ALTER TABLE novels_categories ADD CONSTRAINT FK_95FF6CDF10AA0E6B FOREIGN KEY (novels_id) REFERENCES novels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE novels_categories ADD CONSTRAINT FK_95FF6CDFA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE novels_tags ADD CONSTRAINT FK_67600D7110AA0E6B FOREIGN KEY (novels_id) REFERENCES novels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE novels_tags ADD CONSTRAINT FK_67600D718D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE novels_chapitres ADD CONSTRAINT FK_56B7825510AA0E6B FOREIGN KEY (novels_id) REFERENCES novels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE novels_chapitres ADD CONSTRAINT FK_56B7825520B9AB7E FOREIGN KEY (chapitres_id) REFERENCES chapitres (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE novels DROP FOREIGN KEY FK_9852DDB3D1DE6B2B');
        $this->addSql('ALTER TABLE novels_categories DROP FOREIGN KEY FK_95FF6CDF10AA0E6B');
        $this->addSql('ALTER TABLE novels_categories DROP FOREIGN KEY FK_95FF6CDFA21214B7');
        $this->addSql('ALTER TABLE novels_tags DROP FOREIGN KEY FK_67600D7110AA0E6B');
        $this->addSql('ALTER TABLE novels_tags DROP FOREIGN KEY FK_67600D718D7B4FB4');
        $this->addSql('ALTER TABLE novels_chapitres DROP FOREIGN KEY FK_56B7825510AA0E6B');
        $this->addSql('ALTER TABLE novels_chapitres DROP FOREIGN KEY FK_56B7825520B9AB7E');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE chapitres');
        $this->addSql('DROP TABLE novels');
        $this->addSql('DROP TABLE novels_categories');
        $this->addSql('DROP TABLE novels_tags');
        $this->addSql('DROP TABLE novels_chapitres');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE traducteurs');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
