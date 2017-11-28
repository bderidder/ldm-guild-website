<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171127204353 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE DiscordAuthCode (id INT AUTO_INCREMENT NOT NULL, account INT NOT NULL, state VARCHAR(20) NOT NULL, nonce VARCHAR(100) DEFAULT NULL, authCode VARCHAR(100) DEFAULT NULL, creationDate INT NOT NULL, INDEX IDX_49C72ACF7D3656A4 (account), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE DiscordAccessToken (id INT AUTO_INCREMENT NOT NULL, account INT NOT NULL, state VARCHAR(20) NOT NULL, accessToken VARCHAR(100) DEFAULT NULL, creationDate INT NOT NULL, INDEX IDX_922490F77D3656A4 (account), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE DiscordAuthCode ADD CONSTRAINT FK_49C72ACF7D3656A4 FOREIGN KEY (account) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE DiscordAccessToken ADD CONSTRAINT FK_922490F77D3656A4 FOREIGN KEY (account) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Event CHANGE inviteTime inviteTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', CHANGE startTime startTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', CHANGE endTime endTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE DiscordAuthCode');
        $this->addSql('DROP TABLE DiscordAccessToken');
        $this->addSql('ALTER TABLE Event CHANGE inviteTime inviteTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', CHANGE startTime startTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', CHANGE endTime endTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
    }
}
