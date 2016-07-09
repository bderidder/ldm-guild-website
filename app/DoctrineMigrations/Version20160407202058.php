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

        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z01", "Applications", "Applications from aspiring members")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z02", "General", "General and publicly available information on the guild")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z03", "Info from the officers", "Information from Officers, make sure to read them!")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z04", "Member introductions", "Member introduction and post links to profile pages")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z05", "Other games", "Discussions on other games played by guild members")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z06", "Off topic", "Anything that does not belong in any other forum")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z07", "General", "General posts on PvE")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z08", "Raid tactics", "Discussions on tactics to succeed in raids")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z09", "Dungeons HC/CM", "Discussions on 5-man dungeons")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z10", "Raids", "General discussions on raids")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z11", "Raid rules", "Discussion on La Danse rules for raiding")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z12", "General", "General posts on PvP")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z13", "Arena", "Discussions on Arenas and related topics")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z14", "Rated Battlegrounds", "Discussons on RBGs and related topics")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z15", "World PvP", "Discussions on World PvP and related topics")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z16", "Role Playing", "Role Playing related posts, including stories and similar")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z17", "Events", "Announcement and discussions on guild events")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z18", "Active Concerns", "Officer discussion on current concerns")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z19", "Recruitments information", "Officer discussion on new applications")');
        $this->addSql('INSERT INTO Forum (forumId, name, description) VALUES ("3908cb8b86657cdaf71b020ed0115z20", "La Danse Website", "Announcements and discussions on the website")');
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

        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z01"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z02"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z03"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z04"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z05"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z06"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z07"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z08"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z09"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z10"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z11"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z12"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z13"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z14"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z15"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z16"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z17"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z18"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z19"');
        $this->addSql('DELETE FROM Forum WHERE forumId="3908cb8b86657cdaf71b020ed0115z20"');
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
