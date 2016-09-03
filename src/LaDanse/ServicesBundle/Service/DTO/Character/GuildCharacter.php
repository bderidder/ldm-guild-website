<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;

/**
 * @ExclusionPolicy("none")
 */
class GuildCharacter
{
    /**
     * @var integer
     * @SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     * @SerializedName("name")
     */
    protected $name;

    /**
     * @var integer
     * @SerializedName("level")
     */
    protected $level;

    /**
     * @var StringReference
     * @SerializedName("guildReference")
     */
    protected $guildReference;

    /**
     * @var StringReference
     * @SerializedName("realmReference")
     */
    protected $realmReference;

    /**
     * @var StringReference
     * @SerializedName("gameClassReference")
     */
    protected $gameClassReference;

    /**
     * @var StringReference
     * @SerializedName("gameRaceReference")
     */
    protected $gameRaceReference;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GuildCharacter
     */
    public function setId(int $id): GuildCharacter
    {
        $this->id = $id;
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
     * @return GuildCharacter
     */
    public function setName(string $name): GuildCharacter
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return GuildCharacter
     */
    public function setLevel(int $level): GuildCharacter
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGuildReference(): StringReference
    {
        return $this->guildReference;
    }

    /**
     * @param StringReference $guildReference
     * @return GuildCharacter
     */
    public function setGuildReference(StringReference $guildReference): GuildCharacter
    {
        $this->guildReference = $guildReference;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getRealmReference(): StringReference
    {
        return $this->realmReference;
    }

    /**
     * @param StringReference $realmReference
     * @return GuildCharacter
     */
    public function setRealmReference(StringReference $realmReference): GuildCharacter
    {
        $this->realmReference = $realmReference;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGameClassReference(): StringReference
    {
        return $this->gameClassReference;
    }

    /**
     * @param StringReference $gameClassReference
     * @return GuildCharacter
     */
    public function setGameClassReference(StringReference $gameClassReference): GuildCharacter
    {
        $this->gameClassReference = $gameClassReference;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGameRaceReference(): StringReference
    {
        return $this->gameRaceReference;
    }

    /**
     * @param StringReference $gameRaceReference
     * @return GuildCharacter
     */
    public function setGameRaceReference(StringReference $gameRaceReference): GuildCharacter
    {
        $this->gameRaceReference = $gameRaceReference;
        return $this;
    }
}