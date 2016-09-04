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
class PatchCharacter
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
     * @return PatchCharacter
     */
    public function setName(string $name): PatchCharacter
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
     * @return PatchCharacter
     */
    public function setLevel(int $level): PatchCharacter
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
     * @return PatchCharacter
     */
    public function setGuildReference(StringReference $guildReference): PatchCharacter
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
     * @return PatchCharacter
     */
    public function setGameClass(StringReference $gameClass): PatchCharacter
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
     * @return PatchCharacter
     */
    public function setGameRace(StringReference $gameRace): PatchCharacter
    {
        $this->gameRace = $gameRace;
        return $this;
    }
}