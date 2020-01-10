<?php declare(strict_types=1);

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200110130734 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO GameRace(id, armoryId, faction, name) VALUES ("ac3db1ba-5125-49a9-8fa1-801982b8d83d", 31, "e2308034-6c7f-11e6-94f1-b39df80631e5", "Zandalari Troll")');
        $this->addSql('INSERT INTO GameRace(id, armoryId, faction, name) VALUES ("2fa280ed-e932-4c58-a1c4-984827fb3e9f", 32, "d784ce10-6c7f-11e6-94f1-b39df80631e5", "Kul Tiran")');
        $this->addSql('INSERT INTO GameRace(id, armoryId, faction, name) VALUES ("12648055-0d50-4dd2-8daa-e033e8835ce3", 34, "e2308034-6c7f-11e6-94f1-b39df80631e5", "Mag\'har Orc")');
        $this->addSql('INSERT INTO GameRace(id, armoryId, faction, name) VALUES ("3ded0d1d-9314-452a-ba8d-3eda051db15e", 36, "d784ce10-6c7f-11e6-94f1-b39df80631e5", "Dark Iron Dwarf")');
    }

    /**
     * @param Schema $schema
     *
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM GameClass WHERE id="ac3db1ba-5125-49a9-8fa1-801982b8d83d"');
        $this->addSql('DELETE FROM GameClass WHERE id="2fa280ed-e932-4c58-a1c4-984827fb3e9f"');
        $this->addSql('DELETE FROM GameClass WHERE id="12648055-0d50-4dd2-8daa-e033e8835ce3"');
        $this->addSql('DELETE FROM GameClass WHERE id="3ded0d1d-9314-452a-ba8d-3eda051db15e"');
    }
}
