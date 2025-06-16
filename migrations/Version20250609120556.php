<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609120556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite_friend (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, friendship_id INT DEFAULT NULL, INDEX IDX_695BC09B7E3C61F9 (owner_id), INDEX IDX_695BC09BEA7E2197 (friendship_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_friend ADD CONSTRAINT FK_695BC09B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_friend ADD CONSTRAINT FK_695BC09BEA7E2197 FOREIGN KEY (friendship_id) REFERENCES friendship (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_friend DROP FOREIGN KEY FK_695BC09B7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_friend DROP FOREIGN KEY FK_695BC09BEA7E2197
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite_friend
        SQL);
    }
}
