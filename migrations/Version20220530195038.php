<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530195038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD lieux_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2A2C806AC FOREIGN KEY (lieux_id) REFERENCES lieu (id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2A2C806AC ON sortie (lieux_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2A2C806AC');
        $this->addSql('DROP INDEX IDX_3C3FD3F2A2C806AC ON sortie');
        $this->addSql('ALTER TABLE sortie DROP lieux_id');
    }
}
