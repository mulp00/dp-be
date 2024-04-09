<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407110826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE welcome_message DROP CONSTRAINT FK_DF63CDF6F8FF3D85');
        $this->addSql('ALTER TABLE welcome_message ADD CONSTRAINT FK_DF63CDF6F8FF3D85 FOREIGN KEY (corresponding_message_id) REFERENCES message (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE welcome_message DROP CONSTRAINT fk_df63cdf6f8ff3d85');
        $this->addSql('ALTER TABLE welcome_message ADD CONSTRAINT fk_df63cdf6f8ff3d85 FOREIGN KEY (corresponding_message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
