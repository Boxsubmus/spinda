<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260720045001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE beatmapset_file (id INT AUTO_INCREMENT NOT NULL, file_size INT NOT NULL, sha2_hash VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E880D3D52E554F9 (sha2_hash), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('DROP TABLE beatmap_file');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE beatmap_file (id INT AUTO_INCREMENT NOT NULL, file_size INT NOT NULL, sha2_hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_uca1400_ai_ci`, UNIQUE INDEX UNIQ_77C0BDF92E554F9 (sha2_hash), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_uca1400_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE beatmapset_file');
    }
}
