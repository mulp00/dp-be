<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402143549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id UUID NOT NULL, target_group_id UUID NOT NULL, sequence_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F24FF092E ON message (target_group_id)');
        $this->addSql('COMMENT ON COLUMN message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.target_group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F24FF092E FOREIGN KEY (target_group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE serialized_user_group ADD last_message_sequence_number INT NOT NULL');
        $this->addSql('ALTER TABLE welcome_message ADD corresponding_message_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN welcome_message.corresponding_message_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE welcome_message ADD CONSTRAINT FK_DF63CDF6F8FF3D85 FOREIGN KEY (corresponding_message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DF63CDF6F8FF3D85 ON welcome_message (corresponding_message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE welcome_message DROP CONSTRAINT FK_DF63CDF6F8FF3D85');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F24FF092E');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE serialized_user_group DROP last_message_sequence_number');
        $this->addSql('DROP INDEX UNIQ_DF63CDF6F8FF3D85');
        $this->addSql('ALTER TABLE welcome_message DROP corresponding_message_id');
    }
}
