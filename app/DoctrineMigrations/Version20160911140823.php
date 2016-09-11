<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160911140823 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE TrackedBy DROP FOREIGN KEY FK_C2316E12937AB034');
        $this->addSql('DROP INDEX IDX_C2316E12937AB034 ON TrackedBy');
        $this->addSql('ALTER TABLE TrackedBy CHANGE `character` characterId INT NOT NULL');
        $this->addSql('ALTER TABLE TrackedBy ADD CONSTRAINT FK_C2316E125AF690F3 FOREIGN KEY (characterId) REFERENCES GuildCharacter (id)');
        $this->addSql('CREATE INDEX IDX_C2316E125AF690F3 ON TrackedBy (characterId)');
        $this->addSql('ALTER TABLE InGuild RENAME INDEX idx_ca2244c937ab034 TO IDX_CA2244C664E4F72');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE InGuild RENAME INDEX idx_ca2244c664e4f72 TO IDX_CA2244C937AB034');
        $this->addSql('ALTER TABLE TrackedBy DROP FOREIGN KEY FK_C2316E125AF690F3');
        $this->addSql('DROP INDEX IDX_C2316E125AF690F3 ON TrackedBy');
        $this->addSql('ALTER TABLE TrackedBy CHANGE characterid `character` INT NOT NULL');
        $this->addSql('ALTER TABLE TrackedBy ADD CONSTRAINT FK_C2316E12937AB034 FOREIGN KEY (`character`) REFERENCES GuildCharacter (id)');
        $this->addSql('CREATE INDEX IDX_C2316E12937AB034 ON TrackedBy (`character`)');
    }
}
