<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322231719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE mfkdfpolicy_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mfkdfpolicy (id INT NOT NULL, policy_user_id UUID NOT NULL, policy JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7072B6C4426BA1 ON mfkdfpolicy (policy_user_id)');
        $this->addSql('COMMENT ON COLUMN mfkdfpolicy.policy_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE mfkdfpolicy ADD CONSTRAINT FK_F7072B6C4426BA1 FOREIGN KEY (policy_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE mfkdfpolicy_id_seq CASCADE');
        $this->addSql('ALTER TABLE mfkdfpolicy DROP CONSTRAINT FK_F7072B6C4426BA1');
        $this->addSql('DROP TABLE mfkdfpolicy');
    }
}
