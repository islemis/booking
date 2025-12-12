<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212205924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appartment (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) NOT NULL, number_of_rooms INT NOT NULL, square_meters BIGINT NOT NULL, furnished TINYINT(1) NOT NULL, INDEX IDX_CD632DF07E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, phone_number VARCHAR(20) DEFAULT NULL, userId INT NOT NULL, listingId INT NOT NULL, INDEX IDX_E00CEDDE64B64DCC (userId), INDEX IDX_E00CEDDEB7EE414B (listingId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing (id INT AUTO_INCREMENT NOT NULL, appartment_id INT NOT NULL, owner_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, address VARCHAR(255) NOT NULL, rent_price DOUBLE PRECISION NOT NULL, date_of_publish DATETIME NOT NULL, available_from DATETIME NOT NULL, INDEX IDX_CB0048D42714DC20 (appartment_id), INDEX IDX_CB0048D47E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appartment ADD CONSTRAINT FK_CD632DF07E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE64B64DCC FOREIGN KEY (userId) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB7EE414B FOREIGN KEY (listingId) REFERENCES listing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE listing ADD CONSTRAINT FK_CB0048D42714DC20 FOREIGN KEY (appartment_id) REFERENCES appartment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE listing ADD CONSTRAINT FK_CB0048D47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartment DROP FOREIGN KEY FK_CD632DF07E3C61F9');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE64B64DCC');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB7EE414B');
        $this->addSql('ALTER TABLE listing DROP FOREIGN KEY FK_CB0048D42714DC20');
        $this->addSql('ALTER TABLE listing DROP FOREIGN KEY FK_CB0048D47E3C61F9');
        $this->addSql('DROP TABLE appartment');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE listing');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
