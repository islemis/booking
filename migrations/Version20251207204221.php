<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251207204221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // First, set a default owner for any existing apartments without an owner
        $this->addSql("UPDATE appartment SET owner_id = 1 WHERE owner_id IS NULL");
        
        // Then rename title to type
        $this->addSql('ALTER TABLE appartment CHANGE title type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartment DROP FOREIGN KEY FK_CD632DF07E3C61F9');
        $this->addSql('ALTER TABLE appartment CHANGE owner_id owner_id INT DEFAULT NULL, CHANGE type title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE appartment ADD CONSTRAINT FK_CD632DF07E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
