<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407184549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serialized_user_group ALTER serialized_group TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN serialized_user_group.serialized_group IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE "user" ALTER key_package TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN "user".key_package IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER key_package TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN "user".key_package IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE serialized_user_group ALTER serialized_group TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN serialized_user_group.serialized_group IS NULL');
    }
}
