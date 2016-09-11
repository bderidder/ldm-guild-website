<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\GameData\Guild;

/**
 * @ORM\Entity
 * @ORM\Table(name="InGuild")
 */
class InGuild
{
    const REPOSITORY = 'LaDanseDomainBundle:InGuild';

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $fromTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endTime;

    /**
     * @var Guild $guild
     *
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\GameData\Guild")
     * @ORM\JoinColumn(name="guild", referencedColumnName="id", nullable=false)
     */
    protected $guild;

    /**
     * @var Character $character
     *
     * @ORM\ManyToOne(targetEntity="Character")
     * @ORM\JoinColumn(name="characterId", referencedColumnName="id", nullable=false)
     */
    protected $character;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return InGuild
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFromTime()
    {
        return $this->fromTime;
    }

    /**
     * @param mixed $fromTime
     * @return InGuild
     */
    public function setFromTime($fromTime)
    {
        $this->fromTime = $fromTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     * @return InGuild
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return Guild
     */
    public function getGuild(): Guild
    {
        return $this->guild;
    }

    /**
     * @param Guild $guild
     * @return InGuild
     */
    public function setGuild(Guild $guild): InGuild
    {
        $this->guild = $guild;
        return $this;
    }

    /**
     * @return Character
     */
    public function getCharacter(): Character
    {
        return $this->character;
    }

    /**
     * @param Character $character
     * @return InGuild
     */
    public function setCharacter(Character $character): InGuild
    {
        $this->character = $character;
        return $this;
    }
}