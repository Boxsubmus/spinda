<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260721102927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmapset ADD is_featured TINYINT DEFAULT NULL, ADD featured_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE favorite_beatmapset DROP FOREIGN KEY `FK_5893D01B43EEE4D3`');
        $this->addSql('ALTER TABLE favorite_beatmapset ADD CONSTRAINT FK_5893D01B43EEE4D3 FOREIGN KEY (beatmapset_id) REFERENCES beatmapset (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_beatmapset ON favorite_beatmapset (user_id, beatmapset_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmapset DROP is_featured, DROP featured_at');
        $this->addSql('ALTER TABLE favorite_beatmapset DROP FOREIGN KEY FK_5893D01B43EEE4D3');
        $this->addSql('DROP INDEX uniq_user_beatmapset ON favorite_beatmapset');
        $this->addSql('ALTER TABLE favorite_beatmapset ADD CONSTRAINT `FK_5893D01B43EEE4D3` FOREIGN KEY (beatmapset_id) REFERENCES beatmapset (id)');
    }
}
