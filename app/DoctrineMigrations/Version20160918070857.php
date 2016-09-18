<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160918070857 extends AbstractMigration implements ContainerAwareInterface
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

        $conn->beginTransaction();

        // find all GuildCharacters that have an endTime set

        $stmt = $conn->query(
            "SELECT gc.id, gc.endTime " .
            "FROM GuildCharacter as gc " .
            "WHERE gc.endTime IS NOT NULL"
        );

        $closedCharacters = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $closedCharacters[(string)$row['id']] = $row['endTime'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // update all GuildCharacterVersion that do not have an endTime set but are pointing to a GuildCharacter with an endTime

        foreach($closedCharacters as $id => $endTime)
        {
            $updateStmt = $conn->prepare(
                "UPDATE GuildCharacterVersion SET endTime = :endTime WHERE characterId = :characterId AND endTime IS NULL");
            $updateStmt->bindValue("characterId", $id);
            $updateStmt->bindValue("endTime", $endTime);
            $updateStmt->execute();
        }

        // update all CharacterClaim that do not have an endTime set but are pointing to a GuildCharacter with an endTime

        foreach($closedCharacters as $id => $endTime)
        {
            $updateStmt = $conn->prepare(
                "UPDATE CharacterClaim SET endTime = :endTime WHERE characterId = :characterId AND endTime IS NULL");
            $updateStmt->bindValue("characterId", $id);
            $updateStmt->bindValue("endTime", $endTime);
            $updateStmt->execute();
        }

        // find all Claims that have an endTime set

        $stmt = $conn->query(
            "SELECT c.id, c.endTime " .
            "FROM CharacterClaim as c " .
            "WHERE c.endTime IS NOT NULL"
        );

        $closedClaims = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $closedClaims[(string)$row['id']] = $row['endTime'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // update all PlaysRole that do not have an endTime set but are pointing to a Claim with an endTime

        foreach($closedClaims as $id => $endTime)
        {
            $updateStmt = $conn->prepare(
                "UPDATE PlaysRole SET endTime = :endTime WHERE claimId = :claimId AND endTime IS NULL");
            $updateStmt->bindValue("claimId", $id);
            $updateStmt->bindValue("endTime", $endTime);
            $updateStmt->execute();
        }

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
