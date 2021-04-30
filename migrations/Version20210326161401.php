<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210326161401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel_user (channel_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_11C7753772F5A1AA (channel_id), INDEX IDX_11C77537A76ED395 (user_id), PRIMARY KEY(channel_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE channel_user ADD CONSTRAINT FK_11C7753772F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE channel_user ADD CONSTRAINT FK_11C77537A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64972F5A1AA');
        $this->addSql('DROP INDEX IDX_8D93D64972F5A1AA ON user');
        $this->addSql('ALTER TABLE user DROP channel_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE channel_user');
        $this->addSql('ALTER TABLE `user` ADD channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64972F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64972F5A1AA ON `user` (channel_id)');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
