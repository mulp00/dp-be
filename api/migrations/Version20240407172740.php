<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407172740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ALTER key_package TYPE TEXT');
        $this->addSql('ALTER TABLE "user" ALTER key_store TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN "user".key_package IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN "user".key_store IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER key_package TYPE TEXT');
        $this->addSql('ALTER TABLE "user" ALTER key_store TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN "user".key_package IS NULL');
        $this->addSql('COMMENT ON COLUMN "user".key_store IS NULL');
    }
}
