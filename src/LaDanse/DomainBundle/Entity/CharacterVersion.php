<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\MasterData\GameClass;
use LaDanse\DomainBundle\Entity\MasterData\GameRace;

/**
 * @ORM\Entity
 * @ORM\Table(name="GuildCharacterVersion")
 */
class CharacterVersion
{
    const REPOSITORY = 'LaDanseDomainBundle:CharacterVersion';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Character", inversedBy="versions")
     * @ORM\JoinColumn(name="characterId", referencedColumnName="id", nullable=false)
     */
    protected $character;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=false)
     */
    protected $fromTime;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    protected $endTime;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    protected $level;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\MasterData\GameClass")
     * @ORM\JoinColumn(name="gameClassId", referencedColumnName="id", nullable=false)
     */
    protected $gameClass;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\MasterData\GameRace")
     * @ORM\JoinColumn(name="gameRaceId", referencedColumnName="id", nullable=false)
     */
    protected $gameRace;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $guild;

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
     * Set fromTime
     *
     * @param \DateTime $fromTime
     * @return CharacterVersion
     */
    public function setFromTime($fromTime)
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    /**
     * Get fromTime
     *
     * @return \DateTime 
     */
    public function getFromTime()
    {
        return $this->fromTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return CharacterVersion
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return CharacterVersion
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Set character
     *
     * @param Character $character
     * @return CharacterVersion
     */
    public function setCharacter(Character $character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return Character
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Set gameClass
     *
     * @param GameClass $gameClass
     * @return CharacterVersion
     */
    public function setGameClass(GameClass $gameClass)
    {
        $this->gameClass = $gameClass;

        return $this;
    }

    /**
     * Get gameClass
     *
     * @return GameClass
     */
    public function getGameClass()
    {
        return $this->gameClass;
    }

    /**
     * Set gameRace
     *
     * @param GameRace $gameRace
     * @return CharacterVersion
     */
    public function setGameRace(GameRace $gameRace)
    {
        $this->gameRace = $gameRace;

        return $this;
    }

    /**
     * Get gameRace
     *
     * @return GameRace
     */
    public function getGameRace()
    {
        return $this->gameRace;
    }

    /**
     * @return string
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * @param string $guild
     */
    public function setGuild($guild)
    {
        $this->guild = $guild;
    }

    /**
     * Return true if the given date is within the period of this version
     *
     * @param \DateTime $onDateTime
     *
     * @return bool
     */
    public function isVersionActiveOn(\DateTime $onDateTime)
    {
        if (($this->getFromTime() <= $onDateTime)
            and
            (($this->getEndTime() > $onDateTime) or is_null($this->getEndTime())))
        {
            return true;
        }

        return false;
    }
}
