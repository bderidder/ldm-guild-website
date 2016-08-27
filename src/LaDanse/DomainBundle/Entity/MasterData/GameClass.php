<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\MasterData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="GameClass")
 */
class GameClass
{
    const REPOSITORY = 'LaDanseDomainBundle:MasterData\GameClass';

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
     * @return GameClass
     */
    public function setId(string $id): GameClass
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
     * @return GameClass
     */
    public function setArmoryId(int $armoryId): GameClass
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
     * @return GameClass
     */
    public function setName(string $name): GameClass
    {
        $this->name = $name;
        return $this;
    }
}
