<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212213621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE listing_image ADD listing_id INT NOT NULL, ADD image_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE listing_image ADD CONSTRAINT FK_33D3DCD3D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_33D3DCD3D4619D1A ON listing_image (listing_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE listing_image DROP FOREIGN KEY FK_33D3DCD3D4619D1A');
        $this->addSql('DROP INDEX IDX_33D3DCD3D4619D1A ON listing_image');
        $this->addSql('ALTER TABLE listing_image DROP listing_id, DROP image_path');
    }
}
