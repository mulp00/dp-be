<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331133634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "group" (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "group".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE group_user (group_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A4C98D39FE54D947 ON group_user (group_id)');
        $this->addSql('CREATE INDEX IDX_A4C98D39A76ED395 ON group_user (user_id)');
        $this->addSql('COMMENT ON COLUMN group_user.group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN group_user.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE mfkdfpolicy (id UUID NOT NULL, policy_user_id UUID NOT NULL, policy TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7072B6C4426BA1 ON mfkdfpolicy (policy_user_id)');
        $this->addSql('COMMENT ON COLUMN mfkdfpolicy.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mfkdfpolicy.policy_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE serialized_user_group (id UUID NOT NULL, group_user_id UUID NOT NULL, group_entity_id UUID NOT NULL, serialized_group TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DC9953B6216E8799 ON serialized_user_group (group_user_id)');
        $this->addSql('CREATE INDEX IDX_DC9953B6419621BC ON serialized_user_group (group_entity_id)');
        $this->addSql('COMMENT ON COLUMN serialized_user_group.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN serialized_user_group.group_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN serialized_user_group.group_entity_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, master_key_hash VARCHAR(255) NOT NULL, serialized_identity TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mfkdfpolicy ADD CONSTRAINT FK_F7072B6C4426BA1 FOREIGN KEY (policy_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE serialized_user_group ADD CONSTRAINT FK_DC9953B6216E8799 FOREIGN KEY (group_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE serialized_user_group ADD CONSTRAINT FK_DC9953B6419621BC FOREIGN KEY (group_entity_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE group_user DROP CONSTRAINT FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE group_user DROP CONSTRAINT FK_A4C98D39A76ED395');
        $this->addSql('ALTER TABLE mfkdfpolicy DROP CONSTRAINT FK_F7072B6C4426BA1');
        $this->addSql('ALTER TABLE serialized_user_group DROP CONSTRAINT FK_DC9953B6216E8799');
        $this->addSql('ALTER TABLE serialized_user_group DROP CONSTRAINT FK_DC9953B6419621BC');
        $this->addSql('DROP TABLE "group"');
        $this->addSql('DROP TABLE group_user');
        $this->addSql('DROP TABLE mfkdfpolicy');
        $this->addSql('DROP TABLE serialized_user_group');
        $this->addSql('DROP TABLE "user"');
    }
}
