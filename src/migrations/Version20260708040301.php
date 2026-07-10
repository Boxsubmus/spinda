<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260708040301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmapset ADD file_hash VARCHAR(64) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_465832E658440AD6 ON beatmapset (file_hash)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_465832E658440AD6 ON beatmapset');
        $this->addSql('ALTER TABLE beatmapset DROP file_hash');
    }
}
