<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210329132836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B119D86650F');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11C86596CF');
        $this->addSql('DROP INDEX IDX_D79F6B11C86596CF ON participant');
        $this->addSql('DROP INDEX IDX_D79F6B119D86650F ON participant');
        $this->addSql('ALTER TABLE participant ADD user_id INT DEFAULT NULL, ADD channel_id INT DEFAULT NULL, DROP user_id_id, DROP channel_id_id');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1172F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11A76ED395 ON participant (user_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B1172F5A1AA ON participant (channel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1172F5A1AA');
        $this->addSql('DROP INDEX IDX_D79F6B11A76ED395 ON participant');
        $this->addSql('DROP INDEX IDX_D79F6B1172F5A1AA ON participant');
        $this->addSql('ALTER TABLE participant ADD user_id_id INT DEFAULT NULL, ADD channel_id_id INT DEFAULT NULL, DROP user_id, DROP channel_id');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B119D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11C86596CF FOREIGN KEY (channel_id_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11C86596CF ON participant (channel_id_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B119D86650F ON participant (user_id_id)');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
