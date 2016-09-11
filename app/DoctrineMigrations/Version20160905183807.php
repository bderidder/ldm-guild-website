<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160905183807 extends AbstractMigration implements ContainerAwareInterface
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
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /** @var Connection $conn */
        $conn = $this->container->get('database_connection');

        $migrationFromTime = new \DateTime();

        $sessionLog = [];

        $conn->beginTransaction();

        // find Guild "La Danse Macabre" on realm "Defias Brotherhood"

        $stmt = $conn->query(
            "SELECT g.id " .
            "FROM Guild as g, Realm as r " .
            "WHERE g.name = 'La Danse Macabre'" .
            "        AND" .
            "      g.realm = r.id" .
            "        AND" .
            "      r.name = 'Defias Brotherhood'"
        );

        if ($stmt->rowCount() != 1)
        {
            // nothing to do in this migration
            return;
        }

        $row = $stmt->fetch();

        $guildId = $row['id'];

        $stmt->closeCursor();

        // create a new UUID

        $stmt = $conn->query("SELECT UUID() as uuid");

        $row = $stmt->fetch();

        $uuid = $row['uuid'];

        $stmt->closeCursor();

        // create CharacterSource instance

        $insertStmt = $conn->prepare("INSERT INTO CharacterSource (id, discr) VALUES (:uuid, :discr)");
        $insertStmt->bindValue("uuid", $uuid);
        $insertStmt->bindValue("discr", "GuildSync");
        $insertStmt->execute();

        $insertStmt = $conn->prepare("INSERT INTO GuildSync (id, guild) VALUES (:uuid, :guild)");
        $insertStmt->bindValue("uuid", $uuid);
        $insertStmt->bindValue("guild", $guildId);
        $insertStmt->execute();

        // create a list of Characters and the values of fromTime and endTime (endTime is not null)

        $stmt = $conn->query(
            "SELECT id, fromTime, endTime " .
            "FROM `GuildCharacter` " .
            "WHERE endTime IS NOT NULL"
        );

        $closedCharacters = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $closedCharacters[(string)$row['id']] = (object)[
                'fromTime' => $row['fromTime'],
                'endTime'  => $row['endTime']
            ];


            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // create TrackedBy instance pointing to earlier retrieved Guild, with given fromTime and endTime

        foreach($closedCharacters as $id => $closedCharacter)
        {
            $createStmt = $conn->prepare(
                "INSERT INTO TrackedBy (`id`, `character`, `fromTime`, `endTime`, `characterSource`) " .
                "VALUES (UUID(), :guildCharacter, :fromTime, :endTime, :characterSource)"
            );
            $createStmt->bindValue("guildCharacter", $id);
            $createStmt->bindValue("fromTime", $closedCharacter->fromTime);
            $createStmt->bindValue("endTime", $closedCharacter->endTime);
            $createStmt->bindValue("characterSource", $uuid);
            $createStmt->execute();

            $sessionLog[] = sprintf(
                "Created TrackedBy for character %u starting from %s and ending on %s",
                $id, $closedCharacter->fromTime, $closedCharacter->endTime);
        }

        // create a list of Characters and the value of fromTime, where the endTime is null

        $stmt = $conn->query(
            "SELECT id, fromTime " .
            "FROM `GuildCharacter` " .
            "WHERE endTime IS NULL"
        );

        $openCharacters = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $openCharacters[(string)$row['id']] = $row['fromTime'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // create TrackedBy instance pointing to earlier retrieved Guild, with given fromTime and endTime null

        foreach($openCharacters as $id => $fromTime)
        {
            $createStmt = $conn->prepare(
                "INSERT INTO TrackedBy (`id`, `character`, `fromTime`, `endTime`, `characterSource`) " .
                "VALUES (UUID(), :guildCharacter, :fromTime, null, :characterSource)"
            );
            $createStmt->bindValue("guildCharacter", $id);
            $createStmt->bindValue("fromTime", $fromTime);
            $createStmt->bindValue("characterSource", $uuid);
            $createStmt->execute();

            $sessionLog[] = sprintf(
                "Created TrackedBy for character %u starting from %s with an open ending",
                $id, $fromTime);
        }

        // create a CharacterSyncSession representing this migration

        $insertStmt = $conn->prepare(
            "INSERT INTO CharacterSyncSession (id, fromTime, endTime, log, characterSource) " .
            "VALUES (UUID(), :fromTime, :endTime, :log, :characterSource)"
        );
        $insertStmt->bindValue("fromTime", $migrationFromTime, Type::DATETIME);
        $insertStmt->bindValue("endTime", new \DateTime(), Type::DATETIME);
        $insertStmt->bindValue("log", json_encode($sessionLog));
        $insertStmt->bindValue("characterSource", $uuid);
        $insertStmt->execute();

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