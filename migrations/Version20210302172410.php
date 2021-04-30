<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302172410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_files (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, INDEX IDX_E4F7BB013DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_files ADD CONSTRAINT FK_E4F7BB013DA5256D FOREIGN KEY (image_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user DROP updated_at, DROP image_name, DROP image_original_name, DROP image_mime_type, DROP image_size, DROP image_dimensions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_files');
        $this->addSql('ALTER TABLE `user` ADD updated_at DATETIME DEFAULT NULL, ADD image_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD image_original_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD image_mime_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD image_size INT DEFAULT NULL, ADD image_dimensions LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\'');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
