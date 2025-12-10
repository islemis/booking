<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251207174019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartment ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appartment ADD CONSTRAINT FK_CD632DF07E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CD632DF07E3C61F9 ON appartment (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartment DROP FOREIGN KEY FK_CD632DF07E3C61F9');
        $this->addSql('DROP INDEX IDX_CD632DF07E3C61F9 ON appartment');
        $this->addSql('ALTER TABLE appartment DROP owner_id');
    }
}
