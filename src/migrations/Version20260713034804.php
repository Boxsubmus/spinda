<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260713034804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE beatmapset_comment_vote (id INT AUTO_INCREMENT NOT NULL, type INT DEFAULT NULL, user_id INT NOT NULL, comment_id INT NOT NULL, INDEX IDX_81CA86A8A76ED395 (user_id), INDEX IDX_81CA86A8F8697D13 (comment_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE beatmapset_comment_vote ADD CONSTRAINT FK_81CA86A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE beatmapset_comment_vote ADD CONSTRAINT FK_81CA86A8F8697D13 FOREIGN KEY (comment_id) REFERENCES beatmapset_comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beatmapset_comment_vote DROP FOREIGN KEY FK_81CA86A8A76ED395');
        $this->addSql('ALTER TABLE beatmapset_comment_vote DROP FOREIGN KEY FK_81CA86A8F8697D13');
        $this->addSql('DROP TABLE beatmapset_comment_vote');
    }
}
