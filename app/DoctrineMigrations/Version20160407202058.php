<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160407202058 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z21", "The Walled City", "Kargath, Butcher and Brackenspore")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z22", "Arcane Sanctum", "Tectus, Twin Ogron and Ko\'ragh")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z23", "Imperator", "Imperator Mor\'gok")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z24", "Slagworks", "Oregorger, Gruul and Blast Furnace")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z25", "Black Forge", "Hans and Franz, Flamebender Ka\'graz and Kromog")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z26", "Iron Assembly", "Beastlord Darmac, Operator Thogar and Iron Maidens")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z27", "Blackhand", "Blackhand")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z28", "Hellbreach", "Hellfire Assault, Iron Reaver and Kormrok")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z29", "Halls of Blood", "Hellfire High Council, Kilrogg Deadeye and Gorefiend")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z30", "Bastion of Shadows", "Shadow-Lord Iskar, Socrethar the Eternal and Tyrant Velhari")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z31", "Destructor\'s Rise", "Fel Lord Zakuun, Xhul\'horac and Mannoroth")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z32", "The Black Gate", "Archimonde")');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z21"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z22"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z23"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z24"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z25"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z26"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z27"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z28"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z29"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z30"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z31"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z32"');
    }
}
