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
     *      max = 120,
     *      minMessage = "The level of a character must be between 1 and 120",
     *      maxMessage = "The level of a character must be between 1 and 120"
     * )
     */
    protected $level;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("realmReference")
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $realmReference;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("guildReference")
     * @Assert\Valid()
     */
    protected $guildReference;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("gameClassReference")
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $gameClassReference;

    /**
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("gameRaceReference")
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $gameRaceReference;

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
    public function getRealmReference(): StringReference
    {
        return $this->realmReference;
    }

    /**
     * @param StringReference $realmReference
     * @return PatchCharacter
     */
    public function setRealmReference(StringReference $realmReference): PatchCharacter
    {
        $this->realmReference = $realmReference;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGuildReference()
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
    public function getGameClassReference(): StringReference
    {
        return $this->gameClassReference;
    }

    /**
     * @param StringReference $gameClassReference
     * @return PatchCharacter
     */
    public function setGameClassReference(StringReference $gameClassReference): PatchCharacter
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
     * @return PatchCharacter
     */
    public function setGameRaceReference(StringReference $gameRaceReference): PatchCharacter
    {
        $this->gameRaceReference = $gameRaceReference;
        return $this;
    }
}