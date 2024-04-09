<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405171652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_item (id UUID NOT NULL, target_group_id UUID NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_36417E6E24FF092E ON group_item (target_group_id)');
        $this->addSql('COMMENT ON COLUMN group_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN group_item.target_group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE group_item ADD CONSTRAINT FK_36417E6E24FF092E FOREIGN KEY (target_group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE group_item DROP CONSTRAINT FK_36417E6E24FF092E');
        $this->addSql('DROP TABLE group_item');
    }
}
