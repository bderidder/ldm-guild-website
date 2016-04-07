<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160407202027 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO GameClass(id, name) VALUES (1, "Warrior")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (2, "Paladin")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (3, "Hunter")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (4, "Rogue")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (5, "Priest")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (6, "Death Knight")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (7, "Shaman")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (8, "Mage")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (9, "Warlock")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (10, "Monk")');
        $this->addSql('INSERT INTO GameClass(id, name) VALUES (11, "Druid")');

        $this->addSql('INSERT INTO GameRace(id, name) VALUES (1, "Human")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (2, "Orc")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (3, "Dwarf")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (4, "Night Elf")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (5, "Undead")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (6, "Tauren")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (7, "Gnome")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (8, "Troll")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (9, "Goblin")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (10, "Blood Elf")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (11, "Draenei")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (22, "Worgen")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (24, "Pandaren")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (25, "Pandaran (Alliace)")');
        $this->addSql('INSERT INTO GameRace(id, name) VALUES (26, "Pandaren (Horde)")');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM GameClass WHERE id=1');
        $this->addSql('DELETE FROM GameClass WHERE id=2');
        $this->addSql('DELETE FROM GameClass WHERE id=3');
        $this->addSql('DELETE FROM GameClass WHERE id=4');
        $this->addSql('DELETE FROM GameClass WHERE id=5');
        $this->addSql('DELETE FROM GameClass WHERE id=6');
        $this->addSql('DELETE FROM GameClass WHERE id=7');
        $this->addSql('DELETE FROM GameClass WHERE id=8');
        $this->addSql('DELETE FROM GameClass WHERE id=9');
        $this->addSql('DELETE FROM GameClass WHERE id=10');
        $this->addSql('DELETE FROM GameClass WHERE id=11');

        $this->addSql('DELETE FROM GameRace WHERE id=1');
        $this->addSql('DELETE FROM GameRace WHERE id=2');
        $this->addSql('DELETE FROM GameRace WHERE id=3');
        $this->addSql('DELETE FROM GameRace WHERE id=4');
        $this->addSql('DELETE FROM GameRace WHERE id=5');
        $this->addSql('DELETE FROM GameRace WHERE id=6');
        $this->addSql('DELETE FROM GameRace WHERE id=7');
        $this->addSql('DELETE FROM GameRace WHERE id=8');
        $this->addSql('DELETE FROM GameRace WHERE id=9');
        $this->addSql('DELETE FROM GameRace WHERE id=10');
        $this->addSql('DELETE FROM GameRace WHERE id=11');
        $this->addSql('DELETE FROM GameRace WHERE id=22');
        $this->addSql('DELETE FROM GameRace WHERE id=24');
        $this->addSql('DELETE FROM GameRace WHERE id=25');
        $this->addSql('DELETE FROM GameRace WHERE id=26');
    }
}
