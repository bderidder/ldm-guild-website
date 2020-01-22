<?php declare(strict_types=1);

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200122185919 extends AbstractMigration
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

        $this->addSql('INSERT INTO GameRace(id, armoryId, faction, name) VALUES ("905b2822-5cf3-4c6e-af64-d7c31134eb58", 35, "e2308034-6c7f-11e6-94f1-b39df80631e5", "Vulpera")');
        $this->addSql('INSERT INTO GameRace(id, armoryId, faction, name) VALUES ("18eb9410-7ceb-43c0-a4fe-80041a1bf613", 37, "d784ce10-6c7f-11e6-94f1-b39df80631e5", "Mechagnome")');
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

        $this->addSql('DELETE FROM GameClass WHERE id="905b2822-5cf3-4c6e-af64-d7c31134eb58"');
        $this->addSql('DELETE FROM GameClass WHERE id="18eb9410-7ceb-43c0-a4fe-80041a1bf613"');
    }
}
