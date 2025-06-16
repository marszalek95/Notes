<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609105112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45F6A5458E8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7234A45FA76ED395 ON friendship
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7234A45F6A5458E8 ON friendship
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD sender_id INT DEFAULT NULL, ADD receiver_id INT DEFAULT NULL, DROP user_id, DROP friend_id, DROP status, DROP favorite
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7234A45FF624B39D ON friendship (sender_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7234A45FCD53EDB6 ON friendship (receiver_id)
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
            DROP INDEX IDX_7234A45FF624B39D ON friendship
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7234A45FCD53EDB6 ON friendship
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD user_id INT NOT NULL, ADD friend_id INT NOT NULL, ADD status VARCHAR(255) NOT NULL, ADD favorite TINYINT(1) NOT NULL, DROP sender_id, DROP receiver_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45F6A5458E8 FOREIGN KEY (friend_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7234A45FA76ED395 ON friendship (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7234A45F6A5458E8 ON friendship (friend_id)
        SQL);
    }
}
