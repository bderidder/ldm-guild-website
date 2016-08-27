<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160827175536 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE GameFaction (id CHAR(36) CHARACTER SET \'utf8\' NOT NULL COMMENT \'(DC2Type:guid)\', armoryId INT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('INSERT INTO GameFaction (id, armoryId, name) VALUES ("d784ce10-6c7f-11e6-94f1-b39df80631e5", 0, "Alliance")');
        $this->addSql('INSERT INTO GameFaction (id, armoryId, name) VALUES ("e2308034-6c7f-11e6-94f1-b39df80631e5", 1, "Horde")');
        $this->addSql('INSERT INTO GameFaction (id, armoryId, name) VALUES ("eb93d2de-6c7f-11e6-94f1-b39df80631e5", 2, "Neutral")');

        $this->addSql("ALTER TABLE GameRace ADD faction CHAR(36) CHARACTER SET 'utf8' NULL COMMENT '(DC2Type:guid)' AFTER armoryId");

        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 1");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 2");
        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 3");
        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 4");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 5");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 6");
        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 7");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 8");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 9");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 10");
        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 11");
        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 22");
        $this->addSql("UPDATE GameRace SET faction = 'eb93d2de-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 24");
        $this->addSql("UPDATE GameRace SET faction = 'd784ce10-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 25");
        $this->addSql("UPDATE GameRace SET faction = 'e2308034-6c7f-11e6-94f1-b39df80631e5' WHERE armoryId = 26");

        $this->addSql("ALTER TABLE GameRace CHANGE COLUMN faction faction CHAR(36) CHARACTER SET 'utf8' NOT NULL COMMENT '(DC2Type:guid)'");

        $this->addSql("ALTER TABLE GameRace ADD CONSTRAINT FK_D51A7CF883048B90 FOREIGN KEY (faction) REFERENCES GameFaction (id)");
        $this->addSql("CREATE INDEX IDX_D51A7CF883048B90 ON GameRace (faction)");
    }

    /**
     * @param Schema $schema
     * @throws \Exception
     */
    public function down(Schema $schema)
    {
        throw new \Exception("Migration the schema 'down' is not supported");
    }
}
