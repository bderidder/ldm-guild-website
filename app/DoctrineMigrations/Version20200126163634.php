<?php declare(strict_types=1);

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200126163634 extends AbstractMigration
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

        $this->addSql('UPDATE GameRace SET faction = "d784ce10-6c7f-11e6-94f1-b39df80631e5" WHERE ID = "12648055-0d50-4dd2-8daa-e033e8835ce3"'); // Dark Iron Dwarf
        $this->addSql('UPDATE GameRace SET faction = "e2308034-6c7f-11e6-94f1-b39df80631e5" WHERE ID = "3ded0d1d-9314-452a-ba8d-3eda051db15e"'); // Mag'har Orc
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

        $this->addSql('UPDATE GameRace SET faction = "e2308034-6c7f-11e6-94f1-b39df80631e5" WHERE ID = "12648055-0d50-4dd2-8daa-e033e8835ce3"'); // Dark Iron Dwarf
        $this->addSql('UPDATE GameRace SET faction = "d784ce10-6c7f-11e6-94f1-b39df80631e5" WHERE ID = "3ded0d1d-9314-452a-ba8d-3eda051db15e"'); // Mag'har Orc
    }
}
