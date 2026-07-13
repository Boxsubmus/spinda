<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260713003114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmapset_comment ADD beatmapset_id INT NOT NULL');
        $this->addSql('ALTER TABLE beatmapset_comment ADD CONSTRAINT FK_AD110CFF43EEE4D3 FOREIGN KEY (beatmapset_id) REFERENCES beatmapset (id)');
        $this->addSql('CREATE INDEX IDX_AD110CFF43EEE4D3 ON beatmapset_comment (beatmapset_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmapset_comment DROP FOREIGN KEY FK_AD110CFF43EEE4D3');
        $this->addSql('DROP INDEX IDX_AD110CFF43EEE4D3 ON beatmapset_comment');
        $this->addSql('ALTER TABLE beatmapset_comment DROP beatmapset_id');
    }
}
