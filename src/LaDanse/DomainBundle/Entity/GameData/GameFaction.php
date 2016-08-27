<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\GameData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="GameFaction")
 */
class GameFaction
{
    const REPOSITORY = 'LaDanseDomainBundle:GameData\GameFaction';

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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return GameFaction
     */
    public function setId(string $id): GameFaction
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
     * @return GameFaction
     */
    public function setArmoryId(int $armoryId): GameFaction
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
     * @return GameFaction
     */
    public function setName(string $name): GameFaction
    {
        $this->name = $name;
        return $this;
    }
}
