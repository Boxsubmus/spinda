<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260714055600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_session (session_id VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_8849CBDEA76ED395 (user_id), PRIMARY KEY (session_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_session ADD CONSTRAINT FK_8849CBDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX user_comment_unique ON beatmapset_comment_vote (user_id, comment_id)');
        $this->addSql('ALTER TABLE sessions CHANGE sess_data sess_data LONGBLOB NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_session DROP FOREIGN KEY FK_8849CBDEA76ED395');
        $this->addSql('DROP TABLE user_session');
        $this->addSql('DROP INDEX user_comment_unique ON beatmapset_comment_vote');
        $this->addSql('ALTER TABLE sessions CHANGE sess_data sess_data BLOB NOT NULL');
    }
}
