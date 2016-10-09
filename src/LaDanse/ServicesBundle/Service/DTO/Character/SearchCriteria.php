<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @ExclusionPolicy("none")
 */
class SearchCriteria
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("name")
     */
    protected $name = null;

    /**
     * @var integer
     * @Type("integer")
     * @SerializedName("minLevel")
     */
    protected $minLevel = 1;

    /**
     * @var integer
     * @Type("integer")
     * @SerializedName("maxLevel")
     */
    protected $maxLevel = 110;

    /**
     * @var integer
     * @Type("integer")
     * @SerializedName("raider")
     */
    protected $raider = 0;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("guild")
     */
    protected $guild = null;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("gameClass")
     */
    protected $gameClass = null;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("gameRace")
     */
    protected $gameRace = null;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("gameFaction")
     */
    protected $gameFaction = null;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return SearchCriteria
     */
    public function setName(string $name): SearchCriteria
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinLevel(): int
    {
        return $this->minLevel;
    }

    /**
     * @param int $minLevel
     * @return SearchCriteria
     */
    public function setMinLevel(int $minLevel): SearchCriteria
    {
        $this->minLevel = $minLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLevel(): int
    {
        return $this->maxLevel;
    }

    /**
     * @param int $maxLevel
     * @return SearchCriteria
     */
    public function setMaxLevel(int $maxLevel): SearchCriteria
    {
        $this->maxLevel = $maxLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getRaider(): int
    {
        return $this->raider;
    }

    /**
     * @param int $raider
     * @return SearchCriteria
     */
    public function setRaider(int $raider): SearchCriteria
    {
        $this->raider = $raider;
        return $this;
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
     * @return SearchCriteria
     */
    public function setGuild(string $guild): SearchCriteria
    {
        $this->guild = $guild;
        return $this;
    }

    /**
     * @return string
     */
    public function getGameClass()
    {
        return $this->gameClass;
    }

    /**
     * @param string $gameClass
     * @return SearchCriteria
     */
    public function setGameClass(string $gameClass): SearchCriteria
    {
        $this->gameClass = $gameClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getGameRace()
    {
        return $this->gameRace;
    }

    /**
     * @param string $gameRace
     * @return SearchCriteria
     */
    public function setGameRace(string $gameRace): SearchCriteria
    {
        $this->gameRace = $gameRace;
        return $this;
    }

    /**
     * @return string
     */
    public function getGameFaction()
    {
        return $this->gameFaction;
    }

    /**
     * @param string $gameFaction
     * @return SearchCriteria
     */
    public function setGameFaction(string $gameFaction): SearchCriteria
    {
        $this->gameFaction = $gameFaction;
        return $this;
    }
}