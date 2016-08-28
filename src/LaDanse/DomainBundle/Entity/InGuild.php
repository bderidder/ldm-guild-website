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
     * @ORM\JoinColumn(name="character", referencedColumnName="id", nullable=false)
     */
    protected $character;
}