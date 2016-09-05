<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity\CharacterOrigin;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CharacterSyncSession", options={"collate":"utf8mb4_unicode_ci", "charset":"utf8mb4"})
 */
class CharacterSyncSession
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
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string $log
     */
    protected $log;

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
     * @return CharacterSyncSession
     */
    public function setId(string $id): CharacterSyncSession
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
     * @return CharacterSyncSession
     */
    public function setFromTime(\DateTime $fromTime): CharacterSyncSession
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
     * @return CharacterSyncSession
     */
    public function setEndTime(\DateTime $endTime): CharacterSyncSession
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getLog(): string
    {
        return $this->log;
    }

    /**
     * @param string $log
     * @return CharacterSyncSession
     */
    public function setLog(string $log): CharacterSyncSession
    {
        $this->log = $log;
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
     * @return CharacterSyncSession
     */
    public function setCharacterSource($characterSource) : CharacterSyncSession
    {
        $this->characterSource = $characterSource;
        return $this;
    }
}