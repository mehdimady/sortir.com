<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220609175220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant_participant (participant_source INT NOT NULL, participant_target INT NOT NULL, INDEX IDX_2DAC9694EDF6C1F2 (participant_source), INDEX IDX_2DAC9694F413917D (participant_target), PRIMARY KEY(participant_source, participant_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant_participant ADD CONSTRAINT FK_2DAC9694EDF6C1F2 FOREIGN KEY (participant_source) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant_participant ADD CONSTRAINT FK_2DAC9694F413917D FOREIGN KEY (participant_target) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1151E8871B');
        $this->addSql('DROP INDEX IDX_D79F6B1151E8871B ON participant');
        $this->addSql('ALTER TABLE participant DROP favoris_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participant_participant');
        $this->addSql('ALTER TABLE participant ADD favoris_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1151E8871B FOREIGN KEY (favoris_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D79F6B1151E8871B ON participant (favoris_id)');
    }
}
