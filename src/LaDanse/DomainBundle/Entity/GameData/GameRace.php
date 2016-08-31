<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\GameData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="GameRace")
 */
class GameRace
{
    const REPOSITORY = 'LaDanseDomainBundle:GameData\GameRace';

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string $id
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var integer $armoryId
     */
    protected $armoryId;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     *
     * @var string $name
     */
    protected $name;

    /**
     * @var GameFaction $faction the faction of this race
     *
     * @ORM\ManyToOne(targetEntity="GameFaction")
     * @ORM\JoinColumn(name="faction", referencedColumnName="id", nullable=false)
     */
    protected $faction;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return GameRace
     */
    public function setId(string $id): GameRace
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getArmoryId(): int
    {
        return $this->armoryId;
    }

    /**
     * @param int $armoryId
     * @return GameRace
     */
    public function setArmoryId(int $armoryId): GameRace
    {
        $this->armoryId = $armoryId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return GameRace
     */
    public function setName(string $name): GameRace
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return GameFaction
     */
    public function getFaction(): GameFaction
    {
        return $this->faction;
    }

    /**
     * @param GameFaction $faction
     * @return GameRace
     */
    public function setFaction(GameFaction $faction): GameRace
    {
        $this->faction = $faction;
        return $this;
    }
}
