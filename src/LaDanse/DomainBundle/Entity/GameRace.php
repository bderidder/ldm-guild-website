<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="GameRace")
 */
class GameRace
{
    const REPOSITORY = 'LaDanseDomainBundle:GameRace';
    
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    protected $name;

    /*
	const RACES = {
		1  => 'Human',
		2  => 'Orc',
		3  => 'Dwarf',
		4  => 'Night Elf',
		5  => 'Undead',
		6  => 'Tauren',
		7  => 'Gnome',
		8  => 'Troll',
		9  => 'Goblin',
		10 => 'Blood Elf',
		11 => 'Draenei',
		22 => 'Worgen',
		24 => 'Pandaren (Neutral)',
		25 => 'Pandaren (Alliance)',
		26 => 'Pandaren (Horde)'
	};
	*/

    /**
     * Set id
     *
     * @param integer $id
     * @return GameRace
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return GameRace
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
