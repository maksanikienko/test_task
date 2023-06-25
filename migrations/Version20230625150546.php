<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625150546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_mappings ADD username_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url_mappings ADD CONSTRAINT FK_744A2D90ED766068 FOREIGN KEY (username_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_744A2D90ED766068 ON url_mappings (username_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE url_mappings DROP CONSTRAINT FK_744A2D90ED766068');
        $this->addSql('DROP INDEX IDX_744A2D90ED766068');
        $this->addSql('ALTER TABLE url_mappings DROP username_id');
    }
}
