<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160822175837 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Account CHANGE username username VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE Account CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE Account CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE Account CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Account CHANGE username username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE Account CHANGE username_canonical username_canonical VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE Account CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE Account CHANGE email_canonical email_canonical VARCHAR(255) NOT NULL');
    }
}
