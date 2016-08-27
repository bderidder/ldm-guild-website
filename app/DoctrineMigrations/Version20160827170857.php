<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160827170857 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Guild (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', realm CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(100) NOT NULL, INDEX IDX_B48152AFFA96DBDA (realm), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE Realm (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE Guild ADD CONSTRAINT FK_B48152AFFA96DBDA FOREIGN KEY (realm) REFERENCES Realm (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Guild DROP FOREIGN KEY FK_B48152AFFA96DBDA');
        $this->addSql('DROP TABLE Guild');
        $this->addSql('DROP TABLE Realm');
    }
}
