<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="GameClass")
     * @ORM\JoinColumn(name="gameClassId", referencedColumnName="id", nullable=false)
     */
    protected $gameClass;

    /**
     * @ORM\ManyToOne(targetEntity="GameRace")
     * @ORM\JoinColumn(name="gameRaceId", referencedColumnName="id", nullable=false)
     */
    protected $gameRace;

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
     * @return Character
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
     * @return Character
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
     * @param \LaDanse\DomainBundle\Entity\Character $character
     * @return CharacterVersion
     */
    public function setCharacter(\LaDanse\DomainBundle\Entity\Character $character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return \LaDanse\DomainBundle\Entity\Character 
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Set gameClass
     *
     * @param \LaDanse\DomainBundle\Entity\GameClass $gameClass
     * @return CharacterVersion
     */
    public function setGameClass(\LaDanse\DomainBundle\Entity\GameClass $gameClass)
    {
        $this->gameClass = $gameClass;

        return $this;
    }

    /**
     * Get gameClass
     *
     * @return \LaDanse\DomainBundle\Entity\GameClass 
     */
    public function getGameClass()
    {
        return $this->gameClass;
    }

    /**
     * Set gameRace
     *
     * @param \LaDanse\DomainBundle\Entity\GameRace $gameRace
     * @return CharacterVersion
     */
    public function setGameRace(\LaDanse\DomainBundle\Entity\GameRace $gameRace)
    {
        $this->gameRace = $gameRace;

        return $this;
    }

    /**
     * Get gameRace
     *
     * @return \LaDanse\DomainBundle\Entity\GameRace 
     */
    public function getGameRace()
    {
        return $this->gameRace;
    }
}
