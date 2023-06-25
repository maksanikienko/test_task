<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625154705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_mappings ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url_mappings ADD CONSTRAINT FK_744A2D9019EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_744A2D9019EB6921 ON url_mappings (client_id)');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649baf07af1');
        $this->addSql('DROP INDEX idx_8d93d649baf07af1');
        $this->addSql('ALTER TABLE "user" DROP url_mapping_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE url_mappings DROP CONSTRAINT FK_744A2D9019EB6921');
        $this->addSql('DROP INDEX IDX_744A2D9019EB6921');
        $this->addSql('ALTER TABLE url_mappings DROP client_id');
        $this->addSql('ALTER TABLE "user" ADD url_mapping_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649baf07af1 FOREIGN KEY (url_mapping_id) REFERENCES url_mappings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8d93d649baf07af1 ON "user" (url_mapping_id)');
    }
}
