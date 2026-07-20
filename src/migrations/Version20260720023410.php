<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260720023410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE beatmap_difficulty (id INT AUTO_INCREMENT NOT NULL, count_tap INT NOT NULL, count_hold INT NOT NULL, total_length INT NOT NULL, bpm DOUBLE PRECISION NOT NULL, beatmapset_id INT NOT NULL, INDEX IDX_66E927E343EEE4D3 (beatmapset_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE beatmap_file (id INT AUTO_INCREMENT NOT NULL, file_size INT NOT NULL, sha2_hash VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_77C0BDF92E554F9 (sha2_hash), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE beatmap_difficulty ADD CONSTRAINT FK_66E927E343EEE4D3 FOREIGN KEY (beatmapset_id) REFERENCES beatmapset (id)');
        $this->addSql('DROP INDEX UNIQ_465832E658440AD6 ON beatmapset');
        $this->addSql('ALTER TABLE beatmapset DROP file_hash');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmap_difficulty DROP FOREIGN KEY FK_66E927E343EEE4D3');
        $this->addSql('DROP TABLE beatmap_difficulty');
        $this->addSql('DROP TABLE beatmap_file');
        $this->addSql('ALTER TABLE beatmapset ADD file_hash VARCHAR(64) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_465832E658440AD6 ON beatmapset (file_hash)');
    }
}
