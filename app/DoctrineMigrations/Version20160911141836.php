<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160911141836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE InGuild DROP FOREIGN KEY FK_CA2244C937AB034');
        $this->addSql('DROP INDEX IDX_CA2244C664E4F72 ON InGuild');
        $this->addSql('ALTER TABLE InGuild CHANGE `character` characterId INT NOT NULL');
        $this->addSql('ALTER TABLE InGuild ADD CONSTRAINT FK_CA2244C5AF690F3 FOREIGN KEY (characterId) REFERENCES GuildCharacter (id)');
        $this->addSql('CREATE INDEX IDX_CA2244C5AF690F3 ON InGuild (characterId)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE InGuild DROP FOREIGN KEY FK_CA2244C5AF690F3');
        $this->addSql('DROP INDEX IDX_CA2244C5AF690F3 ON InGuild');
        $this->addSql('ALTER TABLE InGuild CHANGE characterid `character` INT NOT NULL');
        $this->addSql('ALTER TABLE InGuild ADD CONSTRAINT FK_CA2244C937AB034 FOREIGN KEY (`character`) REFERENCES GuildCharacter (id)');
        $this->addSql('CREATE INDEX IDX_CA2244C664E4F72 ON InGuild (`character`)');
    }
}
