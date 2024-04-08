<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407200337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serialized_user_group DROP CONSTRAINT FK_DC9953B6419621BC');
        $this->addSql('ALTER TABLE serialized_user_group ADD CONSTRAINT FK_DC9953B6419621BC FOREIGN KEY (group_entity_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE serialized_user_group DROP CONSTRAINT fk_dc9953b6419621bc');
        $this->addSql('ALTER TABLE serialized_user_group ADD CONSTRAINT fk_dc9953b6419621bc FOREIGN KEY (group_entity_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
