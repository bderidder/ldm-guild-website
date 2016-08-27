<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migration for GameClass
 *
 * The old "id" field is moved to "armoryId" and a new "id" field is introduced.
 * Update all referring entities (CharacterVersion)
 */
class Version20160827103917 extends AbstractMigration implements ContainerAwareInterface
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
        $conn->executeUpdate("ALTER TABLE GameClass ADD COLUMN armoryId INT(11) NULL");

        // copy "id" value to "armoryId"
        $conn->executeUpdate("UPDATE GameClass SET armoryId=id");

        // remove foreign key from CharacterVersion for GameClass
        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion DROP FOREIGN KEY FK_A70EBD18F3B4E37B");
        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion DROP INDEX IDX_A70EBD18F3B4E37B");

        // add "newId" to char(36) with default null
        $conn->executeUpdate("ALTER TABLE GameClass ADD newId CHAR(36) NULL COMMENT '(DC2Type:guid)'");

        // generate value for "newId" using MySQL UUID()
        $conn->executeUpdate("UPDATE GameClass SET newId=UUID()");

        // change primary key to "newId" and set the new "armoryId" to NOT NULL
        $conn->executeUpdate(
            "ALTER TABLE GameClass " .
            "DROP COLUMN id," .
            "CHANGE COLUMN newId id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)' FIRST," .
            "CHANGE COLUMN armoryId armoryId INT(11) NOT NULL AFTER id," .
            "DROP PRIMARY KEY," .
            "ADD PRIMARY KEY (id)"
        );

        // keep track of CharacterVersion references to GameClass
        $stmt = $conn->query("SELECT id, armoryId FROM GameClass");

        $mappings = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $mappings[(string)$row['armoryId']] = $row['id'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // add column "newGameClassId"
        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion ADD COLUMN newGameClassId CHAR(36) NULL COMMENT '(DC2Type:guid)' AFTER gameClassId");

        // populate "newGameClassId" using the mapping stored earlier
        $stmt = $conn->query("SELECT id, gameClassId FROM GuildCharacterVersion ORDER BY id ASC");

        $updates = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $updates[(string)$row['id']] = $row['gameClassId'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        foreach($updates as $id => $gameClassId)
        {
            $updateStmt = $conn->prepare("UPDATE GuildCharacterVersion SET newGameClassId = :uuidGameClass WHERE id = :id");
            $updateStmt->bindValue("id", $id);
            $updateStmt->bindValue("uuidGameClass", $mappings[$gameClassId]);
            $updateStmt->execute();
        }

        // drop column "gameClassId"
        // alter column "newGameClassId" to "gameClassId"
        $conn->executeUpdate(
            "ALTER TABLE GuildCharacterVersion " .
            "DROP COLUMN gameClassId, " .
            "CHANGE COLUMN newGameClassId gameClassId CHAR(36) NOT NULL COMMENT '(DC2Type:guid)';"
        );

        // recreate foreign key from "gameClassId" to GameClass
        $conn->executeUpdate(
            "ALTER TABLE GuildCharacterVersion " .
            "ADD INDEX IDX_A70EBD18F3B4E37B (gameClassId ASC)");

        $conn->executeUpdate(
            "ALTER TABLE GuildCharacterVersion " .
            "ADD CONSTRAINT FK_A70EBD18F3B4E37B " .
            "   FOREIGN KEY (gameClassId) " .
            "   REFERENCES GameClass (id) " .
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
