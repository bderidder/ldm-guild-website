<?php

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExclusionPolicy("none")
 */
class PatchGuildCharacter
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("name")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @Assert\NotNull()
     */
    protected $guild;

    /**
     * @var int
     * @Assert\Range(
     *      min = 1,
     *      max = 110,
     *      minMessage = "The level of a character must be between 1 and 110",
     *      maxMessage = "The level of a character must be between 1 and 110"
     * )
     */
    protected $level;

    /**
     * @var StringReference
     * @Assert\NotNull()
     */
    protected $gameClass;

    /**
     * @var StringReference
     * @Assert\NotNull()
     */
    protected $gameRace;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setGuild($guild)
    {
        $this->guild = $guild;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
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
     */
    public function setGameClass($gameClass)
    {
        $this->gameClass = $gameClass;
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
     */
    public function setGameRace($gameRace)
    {
        $this->gameRace = $gameRace;
    }
}