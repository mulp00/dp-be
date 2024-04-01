<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401184935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "group" ADD creator_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN "group".creator_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "group" ADD CONSTRAINT FK_6DC044C561220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6DC044C561220EA6 ON "group" (creator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "group" DROP CONSTRAINT FK_6DC044C561220EA6');
        $this->addSql('DROP INDEX IDX_6DC044C561220EA6');
        $this->addSql('ALTER TABLE "group" DROP creator_id');
    }
}
