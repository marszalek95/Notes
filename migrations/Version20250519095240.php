<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519095240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE friendship (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_7234A45FF624B39D (sender_id), INDEX IDX_7234A45FCD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FCD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE friendship
        SQL);
    }
}
