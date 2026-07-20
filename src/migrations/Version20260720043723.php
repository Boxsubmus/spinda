<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260720043723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE beatmap_difficulty_owner (id INT AUTO_INCREMENT NOT NULL, beatmap_difficulty_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_414429F8CEF64316 (beatmap_difficulty_id), INDEX IDX_414429F8A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE beatmap_difficulty_owner ADD CONSTRAINT FK_414429F8CEF64316 FOREIGN KEY (beatmap_difficulty_id) REFERENCES beatmap_difficulty (id)');
        $this->addSql('ALTER TABLE beatmap_difficulty_owner ADD CONSTRAINT FK_414429F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmap_difficulty_owner DROP FOREIGN KEY FK_414429F8CEF64316');
        $this->addSql('ALTER TABLE beatmap_difficulty_owner DROP FOREIGN KEY FK_414429F8A76ED395');
        $this->addSql('DROP TABLE beatmap_difficulty_owner');
    }
}
