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
     * @Type(StringReference::class)
     * @SerializedName("guildReference")
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $guildReference;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("gameClass")
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $gameClass;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("gameRace")
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $gameRace;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PatchGuildCharacter
     */
    public function setName(string $name): PatchGuildCharacter
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
     * @return PatchGuildCharacter
     */
    public function setLevel(int $level): PatchGuildCharacter
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
     * @return PatchGuildCharacter
     */
    public function setGuildReference(StringReference $guildReference): PatchGuildCharacter
    {
        $this->guildReference = $guildReference;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGameClass(): StringReference
    {
        return $this->gameClass;
    }

    /**
     * @param StringReference $gameClass
     * @return PatchGuildCharacter
     */
    public function setGameClass(StringReference $gameClass): PatchGuildCharacter
    {
        $this->gameClass = $gameClass;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGameRace(): StringReference
    {
        return $this->gameRace;
    }

    /**
     * @param StringReference $gameRace
     * @return PatchGuildCharacter
     */
    public function setGameRace(StringReference $gameRace): PatchGuildCharacter
    {
        $this->gameRace = $gameRace;
        return $this;
    }
}