<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

class ArmoryObject
{
    /** @var string */
    private $name;

    /** @var int */
    private $level;

    /** @var int */
    private $classArmoryId;

    /** @var int */
    private $raceArmoryId;

    /** @var string */
    private $guildName;

    /** @var string */
    private $realmName;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ArmoryObject
     */
    public function setName(string $name): ArmoryObject
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
     * @return ArmoryObject
     */
    public function setLevel(int $level): ArmoryObject
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return int
     */
    public function getClassArmoryId(): int
    {
        return $this->classArmoryId;
    }

    /**
     * @param int $classArmoryId
     * @return ArmoryObject
     */
    public function setClassArmoryId(int $classArmoryId): ArmoryObject
    {
        $this->classArmoryId = $classArmoryId;
        return $this;
    }

    /**
     * @return int
     */
    public function getRaceArmoryId(): int
    {
        return $this->raceArmoryId;
    }

    /**
     * @param int $raceArmoryId
     * @return ArmoryObject
     */
    public function setRaceArmoryId(int $raceArmoryId): ArmoryObject
    {
        $this->raceArmoryId = $raceArmoryId;
        return $this;
    }

    /**
     * @return string
     */
    public function getGuildName(): string
    {
        return $this->guildName;
    }

    /**
     * @param string $guildName
     * @return ArmoryObject
     */
    public function setGuildName(string $guildName): ArmoryObject
    {
        $this->guildName = $guildName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRealmName(): string
    {
        return $this->realmName;
    }

    /**
     * @param string $realmName
     * @return ArmoryObject
     */
    public function setRealmName(string $realmName): ArmoryObject
    {
        $this->realmName = $realmName;
        return $this;
    }
}