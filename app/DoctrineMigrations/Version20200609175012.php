<?php declare(strict_types=1);

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200609175012 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("2577e58ab330455ba3096690d56b5fc7", "Gathering & Crafting", "Everything you want to know about professions")');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM Forum WHERE forumId="2577e58ab330455ba3096690d56b5fc7"');
    }
}
