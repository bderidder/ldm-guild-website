<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\GameData\Realm;

/**
 * @ORM\Entity
 * @ORM\Table(name="GuildCharacter")
 */
class Character
{
    const REPOSITORY = 'LaDanseDomainBundle:Character';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var Realm $realm
     *
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\GameData\Realm")
     * @ORM\JoinColumn(name="realm", referencedColumnName="id", nullable=false)
     */
    protected $realm;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=false)
     */
    protected $fromTime;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    protected $endTime;

    /**
     * @ORM\OneToMany(targetEntity="CharacterVersion", mappedBy="character", cascade={"persist", "remove"})
     */
    protected $versions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Character
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

    /**
     * @return Realm
     */
    public function getRealm(): Realm
    {
        return $this->realm;
    }

    /**
     * @param Realm $realm
     * @return Character
     */
    public function setRealm(Realm $realm): Character
    {
        $this->realm = $realm;
        return $this;
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
     * Add versions
     *
     * @param CharacterVersion $versions
     * @return Character
     */
    public function addVersion(CharacterVersion $versions)
    {
        $this->versions[] = $versions;

        return $this;
    }

    /**
     * Remove versions
     *
     * @param CharacterVersion $versions
     */
    public function removeVersion(CharacterVersion $versions)
    {
        $this->versions->removeElement($versions);
    }

    /**
     * Get versions
     *
     * @return Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Returns the CharacterVersion that is (was) active on the given date
     *
     * @param \DateTime $onDateTime
     *
     * @return CharacterVersion|null
     */
    public function getVersionForDate(\DateTime $onDateTime)
    {
        if (is_null($onDateTime))
        {
            return $this->versions[count($this->versions) - 1];
        }

        $activeVersion = null;

        /** @var $version CharacterVersion */
        foreach($this->versions as $version)
        {
            if ($version->isVersionActiveOn($onDateTime))
            {
                $activeVersion = $version;
            }
        }

        return $activeVersion;
    }
}
