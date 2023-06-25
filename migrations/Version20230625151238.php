<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625151238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_mappings DROP CONSTRAINT fk_744a2d90ed766068');
        $this->addSql('DROP INDEX idx_744a2d90ed766068');
        $this->addSql('ALTER TABLE url_mappings DROP username_id');
        $this->addSql('ALTER TABLE "user" ADD url_mapping_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649BAF07AF1 FOREIGN KEY (url_mapping_id) REFERENCES url_mappings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649BAF07AF1 ON "user" (url_mapping_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649BAF07AF1');
        $this->addSql('DROP INDEX IDX_8D93D649BAF07AF1');
        $this->addSql('ALTER TABLE "user" DROP url_mapping_id');
        $this->addSql('ALTER TABLE url_mappings ADD username_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url_mappings ADD CONSTRAINT fk_744a2d90ed766068 FOREIGN KEY (username_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_744a2d90ed766068 ON url_mappings (username_id)');
    }
}
