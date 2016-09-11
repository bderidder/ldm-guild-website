<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\CharacterOrigin;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\Character;

/**
 * @ORM\Entity
 * @ORM\Table(name="TrackedBy", options={"collate":"utf8mb4_unicode_ci", "charset":"utf8mb4"})
 */
class TrackedBy
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string $id
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var \DateTime $fromTime
     */
    protected $fromTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime $endTime
     */
    protected $endTime;

    /**
     * @ORM\ManyToOne(targetEntity=Character::class)
     * @ORM\JoinColumn(name="characterId", referencedColumnName="id", nullable=false)
     *
     * @var Character $character
     */
    protected $character;

    /**
     * @ORM\ManyToOne(targetEntity=CharacterSource::class)
     * @ORM\JoinColumn(name="characterSource", referencedColumnName="id", nullable=false)
     *
     * @var CharacterSource $characterSource
     */
    protected $characterSource;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return TrackedBy
     */
    public function setId(string $id): TrackedBy
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFromTime(): \DateTime
    {
        return $this->fromTime;
    }

    /**
     * @param \DateTime $fromTime
     * @return TrackedBy
     */
    public function setFromTime(\DateTime $fromTime): TrackedBy
    {
        $this->fromTime = $fromTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     * @return TrackedBy
     */
    public function setEndTime(\DateTime $endTime): TrackedBy
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return Character
     */
    public function getCharacter() : Character
    {
        return $this->character;
    }

    /**
     * @param Character $character
     * @return TrackedBy
     */
    public function setCharacter(Character $character) : TrackedBy
    {
        $this->character = $character;
        return $this;
    }

    /**
     * @return CharacterSource
     */
    public function getCharacterSource() : CharacterSource
    {
        return $this->characterSource;
    }

    /**
     * @param CharacterSource $characterSource
     * @return TrackedBy
     */
    public function setCharacterSource(CharacterSource $characterSource) : TrackedBy
    {
        $this->characterSource = $characterSource;
        return $this;
    }
}