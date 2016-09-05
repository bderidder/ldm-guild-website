<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160905180554 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CharacterSource (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(100) NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CharacterSyncSession (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, log LONGTEXT DEFAULT NULL, characterSource CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_EC73362CDD71BB (characterSource), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE GuildSync (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', guild CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_18BD775675407DAB (guild), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE TrackedBy (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', `character` INT NOT NULL, fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, characterSource CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_C2316E12937AB034 (`character`), INDEX IDX_C2316E122CDD71BB (characterSource), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE WoWProfileSync (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', account INT NOT NULL, INDEX IDX_70D670C87D3656A4 (account), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CharacterSyncSession ADD CONSTRAINT FK_EC73362CDD71BB FOREIGN KEY (characterSource) REFERENCES CharacterSource (id)');
        $this->addSql('ALTER TABLE GuildSync ADD CONSTRAINT FK_18BD775675407DAB FOREIGN KEY (guild) REFERENCES Guild (id)');
        $this->addSql('ALTER TABLE GuildSync ADD CONSTRAINT FK_18BD7756BF396750 FOREIGN KEY (id) REFERENCES CharacterSource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE TrackedBy ADD CONSTRAINT FK_C2316E12937AB034 FOREIGN KEY (`character`) REFERENCES GuildCharacter (id)');
        $this->addSql('ALTER TABLE TrackedBy ADD CONSTRAINT FK_C2316E122CDD71BB FOREIGN KEY (characterSource) REFERENCES CharacterSource (id)');
        $this->addSql('ALTER TABLE WoWProfileSync ADD CONSTRAINT FK_70D670C87D3656A4 FOREIGN KEY (account) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE WoWProfileSync ADD CONSTRAINT FK_70D670C8BF396750 FOREIGN KEY (id) REFERENCES CharacterSource (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CharacterSyncSession DROP FOREIGN KEY FK_EC73362CDD71BB');
        $this->addSql('ALTER TABLE GuildSync DROP FOREIGN KEY FK_18BD7756BF396750');
        $this->addSql('ALTER TABLE TrackedBy DROP FOREIGN KEY FK_C2316E122CDD71BB');
        $this->addSql('ALTER TABLE WoWProfileSync DROP FOREIGN KEY FK_70D670C8BF396750');
        $this->addSql('DROP TABLE CharacterSource');
        $this->addSql('DROP TABLE CharacterSyncSession');
        $this->addSql('DROP TABLE GuildSync');
        $this->addSql('DROP TABLE TrackedBy');
        $this->addSql('DROP TABLE WoWProfileSync');
    }
}
