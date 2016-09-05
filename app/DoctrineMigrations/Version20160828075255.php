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
class Version20160828075255 extends AbstractMigration implements ContainerAwareInterface
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

        $conn->executeUpdate('CREATE TABLE Guild (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', realm CHAR(36) NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(100) NOT NULL, INDEX IDX_B48152AFFA96DBDA (realm), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $conn->executeUpdate('CREATE TABLE Realm (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $conn->executeUpdate('CREATE TABLE InGuild (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', guild CHAR(36) NULL COMMENT \'(DC2Type:guid)\', `character` INT NULL, fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, INDEX IDX_CA2244C75407DAB (guild), INDEX IDX_CA2244C937AB034 (`character`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');

        // fetch all guilds used in the current database (this should actually be only one)
        $stmt = $conn->query("SELECT DISTINCT(guild) FROM GuildCharacterVersion");

        $usedGuilds = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $usedGuilds[] = $row['guild'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // fetch all realms used in the current database
        $stmt = $conn->query("SELECT DISTINCT(realm) FROM GuildCharacter");

        $usedRealms = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $usedRealms[] = $row['realm'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // create all guilds in the database

        foreach($usedGuilds as $guild)
        {
            $insertStmt = $conn->prepare("INSERT INTO Guild (id, realm, name) VALUES (UUID(), '', :name)");
            $insertStmt->bindValue("name", $guild);
            $insertStmt->execute();
        }

        // get all guilds we just created

        $stmt = $conn->query("SELECT id, name FROM Guild");

        $usedGuilds = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $usedGuilds[$row['name']] = $row['id'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // create all realm in the database

        foreach($usedRealms as $realm)
        {
            $insertStmt = $conn->prepare("INSERT INTO Realm (id, name) VALUES (UUID(), :name)");
            $insertStmt->bindValue("name", $realm);
            $insertStmt->execute();
        }

        // get all realms we just created

        $stmt = $conn->query("SELECT id, name FROM Realm");

        $usedRealms = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $usedRealms[$row['name']] = $row['id'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        // set the realm of all guilds to "Defias Brotherhood"

        $defiasId = $usedRealms['Defias Brotherhood'];

        $insertStmt = $conn->prepare("UPDATE Guild SET realm = :realm");
        $insertStmt->bindValue("realm", $defiasId);
        $insertStmt->execute();

        $conn->executeUpdate("ALTER TABLE GuildCharacterVersion DROP COLUMN guild");

        // update current "realm" in GuildCharacter to the realm id

        $stmt = $conn->query("SELECT id, realm FROM GuildCharacter");

        $characterRealms = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $characterRealms[(string)$row['id']] = $row['realm'];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        $conn->executeUpdate("ALTER TABLE GuildCharacter CHANGE COLUMN realm realm CHAR(36) CHARACTER SET 'utf8' COLLATE utf8_unicode_ci NOT NULL");

        foreach($characterRealms as $characterId => $realmName)
        {
            $insertStmt = $conn->prepare("UPDATE GuildCharacter SET realm = :realmId WHERE id = :id");
            $insertStmt->bindValue("id", $characterId);
            $insertStmt->bindValue("realmId", $usedRealms[$characterRealms[$characterId]]);
            $insertStmt->execute();
        }

        /*
         * Create InGuild for every record in GuildCharacter
         *  - Hard coded to 'La Danse Macabre'
         *  - if the GuildCharacter has an endTime, use that for the endTime of the InGuild instance
         */

        $stmt = $conn->query("SELECT id FROM Guild WHERE name = 'La Danse Macabre'");
        $row = $stmt->fetch();

        $ldmGuildId = $row['id'];

        $stmt = $conn->query("SELECT id, fromTime, endTime FROM GuildCharacter");

        $guildCharacters = [];

        $row = $stmt->fetch();
        while ($row)
        {
            $guildCharacters[(string)$row['id']] = [
                'fromTime' => $row['fromTime'],
                'endTime' => $row['endTime']
            ];

            $row = $stmt->fetch();
        }

        $stmt->closeCursor();

        foreach($guildCharacters as $characterId => $times)
        {
            $insertStmt = $conn->prepare(
                'INSERT INTO InGuild (`id`, `guild`, `character`, `fromTime`, `endTime`) ' .
                'VALUES (UUID(), :guildId, :characterId, :fromTime, :endTime)');
            $insertStmt->bindValue("guildId", $ldmGuildId);
            $insertStmt->bindValue("characterId", $characterId);
            $insertStmt->bindValue("fromTime", $times['fromTime']);
            $insertStmt->bindValue("endTime", $times['endTime']);
            $insertStmt->execute();
        }

        // set all columns that are intended to be a foreign key to not null, added indexes and foreign keys

        $conn->executeUpdate("ALTER TABLE GuildCharacter CHANGE realm realm CHAR(36) NOT NULL COMMENT '(DC2Type:guid)'");
        $conn->executeUpdate("ALTER TABLE GuildCharacter ADD CONSTRAINT FK_92AF3B34FA96DBDA FOREIGN KEY (realm) REFERENCES Realm (id)");
        $conn->executeUpdate("CREATE INDEX IDX_92AF3B34FA96DBDA ON GuildCharacter (realm)");
        $conn->executeUpdate("ALTER TABLE Guild CHANGE realm realm CHAR(36) NOT NULL COMMENT '(DC2Type:guid)'");
        $conn->executeUpdate("ALTER TABLE Guild ADD CONSTRAINT FK_B48152AFFA96DBDA FOREIGN KEY (realm) REFERENCES Realm (id)");
        $conn->executeUpdate("ALTER TABLE InGuild CHANGE guild guild CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', CHANGE `character` `character` INT NOT NULL");
        $conn->executeUpdate("ALTER TABLE InGuild ADD CONSTRAINT FK_CA2244C75407DAB FOREIGN KEY (guild) REFERENCES Guild (id)");
        $conn->executeUpdate("ALTER TABLE InGuild ADD CONSTRAINT FK_CA2244C937AB034 FOREIGN KEY (`character`) REFERENCES GuildCharacter (id)");
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
