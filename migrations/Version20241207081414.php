<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241207081414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if column exists before adding
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) UNIQUE');
        
        // Generate default usernames for existing users
        $this->addSql('UPDATE user SET username = CONCAT("user_", id) WHERE username IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP username');
    }
}
