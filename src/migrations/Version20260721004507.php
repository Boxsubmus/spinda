<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260721004507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_beatmapset (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, beatmapset_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5893D01B43EEE4D3 (beatmapset_id), INDEX IDX_5893D01BA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE favorite_beatmapset ADD CONSTRAINT FK_5893D01B43EEE4D3 FOREIGN KEY (beatmapset_id) REFERENCES beatmapset (id)');
        $this->addSql('ALTER TABLE favorite_beatmapset ADD CONSTRAINT FK_5893D01BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_beatmapset DROP FOREIGN KEY FK_5893D01B43EEE4D3');
        $this->addSql('ALTER TABLE favorite_beatmapset DROP FOREIGN KEY FK_5893D01BA76ED395');
        $this->addSql('DROP TABLE favorite_beatmapset');
    }
}
