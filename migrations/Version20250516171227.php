<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516171227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE note_shared_with (note_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4DF1E1326ED0855 (note_id), INDEX IDX_4DF1E13A76ED395 (user_id), PRIMARY KEY(note_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_shared_with ADD CONSTRAINT FK_4DF1E1326ED0855 FOREIGN KEY (note_id) REFERENCES note (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_shared_with ADD CONSTRAINT FK_4DF1E13A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_user DROP FOREIGN KEY FK_2DE9C71126ED0855
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_user DROP FOREIGN KEY FK_2DE9C711A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE note_user
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE note_user (note_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2DE9C71126ED0855 (note_id), INDEX IDX_2DE9C711A76ED395 (user_id), PRIMARY KEY(note_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_user ADD CONSTRAINT FK_2DE9C71126ED0855 FOREIGN KEY (note_id) REFERENCES note (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_user ADD CONSTRAINT FK_2DE9C711A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_shared_with DROP FOREIGN KEY FK_4DF1E1326ED0855
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note_shared_with DROP FOREIGN KEY FK_4DF1E13A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE note_shared_with
        SQL);
    }
}
