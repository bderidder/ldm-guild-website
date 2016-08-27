<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migration for GameRace
 *
 * The old "id" field is moved to "armoryId" and a new "id" field is introduced.
 * Update all referring entities (CharacterVersion)
 */
class Version20160827135100 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface $container */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // it all happens in postUp as we can't just change the schema
        // we keep a dummy SQL statement here to avoid warnings
        $this->addSql('SELECT 1');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        /** @var Connection $conn */
        $conn = $this->container->get('database_connection');

        $conn->beginTransaction();

        // add "armoryId" field to GameClass
        $conn->executeUpdate("ALTER TABLE GameRace ADD COLUMN armoryId INT(11) NULL");

        // copy "id" value to "armoryId"
        $conn->executeUpdate("UPDATE GameRace SET armoryId=id");

        // remove foreign key from CharacterVersion for GameClass
        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion DROP FOREIGN KEY FK_A70EBD18E036C39A");
        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion DROP INDEX IDX_A70EBD18E036C39A");

        // add "newId" to char(36) with default null
        $conn->executeUpdate("ALTER TABLE GameRace ADD newId CHAR(36) NULL COMMENT '(DC2Type:guid)'");

        // generate value for "newId" using MySQL UUID()
        $conn->executeUpdate("UPDATE GameRace SET newId=UUID()");

        // change primary key to "newId" and set the new "armoryId" to NOT NULL
        $conn->executeUpdate(
            "ALTER TABLE GameRace " .
            "DROP COLUMN id," .
            "CHANGE COLUMN newId id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)' FIRST," .
            "CHANGE COLUMN armoryId armoryId INT(11) NOT NULL AFTER id," .
            "DROP PRIMARY KEY," .
            "ADD PRIMARY KEY (id)"
        );

        // keep track of CharacterVersion references to GameClass
        $stmt = $conn->query("SELECT id, armoryId FROM GameRace");

        $mappings = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $mappings[(string)$row['armoryId']] = $row['id'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // add column "newGameClassId"
        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion ADD COLUMN newGameRaceId CHAR(36) NULL COMMENT '(DC2Type:guid)' AFTER gameRaceId");

        // populate "newGameClassId" using the mapping stored earlier
        $stmt = $conn->query("SELECT id, gameRaceId FROM GuildCharacterVersion ORDER BY id ASC");

        $updates = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $updates[(string)$row['id']] = $row['gameRaceId'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        foreach($updates as $id => $gameRaceId)
        {
            $updateStmt = $conn->prepare("UPDATE GuildCharacterVersion SET newGameRaceId = :uuidRaceClass WHERE id = :id");
            $updateStmt->bindValue("id", $id);
            $updateStmt->bindValue("uuidRaceClass", $mappings[$gameRaceId]);
            $updateStmt->execute();
        }

        // drop column "gameClassId"
        // alter column "newGameClassId" to "gameClassId"
        $conn->executeUpdate(
            "ALTER TABLE GuildCharacterVersion " .
            "DROP COLUMN gameRaceId, " .
            "CHANGE COLUMN newGameRaceId gameRaceId CHAR(36) NOT NULL COMMENT '(DC2Type:guid)';"
        );

        // recreate foreign key from "gameClassId" to GameClass
        $conn->executeUpdate(
            "ALTER TABLE GuildCharacterVersion " .
            "ADD INDEX IDX_A70EBD18E036C39A (gameRaceId ASC)");

        $conn->executeUpdate(
            "ALTER TABLE GuildCharacterVersion " .
            "ADD CONSTRAINT FK_A70EBD18E036C39A " .
            "   FOREIGN KEY (gameRaceId) " .
            "   REFERENCES GameRace (id) " .
            "   ON DELETE NO ACTION " .
            "   ON UPDATE NO ACTION"
        );

        $conn->commit();
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
