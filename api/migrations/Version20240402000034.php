<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402000034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE welcome_message_id_seq CASCADE');
        $this->addSql('CREATE TABLE welcome_message (id UUID NOT NULL, recipient_id UUID NOT NULL, target_group_id UUID NOT NULL, message TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DF63CDF6E92F8F78 ON welcome_message (recipient_id)');
        $this->addSql('CREATE INDEX IDX_DF63CDF624FF092E ON welcome_message (target_group_id)');
        $this->addSql('COMMENT ON COLUMN welcome_message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN welcome_message.recipient_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN welcome_message.target_group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE welcome_message ADD CONSTRAINT FK_DF63CDF6E92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE welcome_message ADD CONSTRAINT FK_DF63CDF624FF092E FOREIGN KEY (target_group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE welcome_message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE welcome_message DROP CONSTRAINT FK_DF63CDF6E92F8F78');
        $this->addSql('ALTER TABLE welcome_message DROP CONSTRAINT FK_DF63CDF624FF092E');
        $this->addSql('DROP TABLE welcome_message');
    }
}
