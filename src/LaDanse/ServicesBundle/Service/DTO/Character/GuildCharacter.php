<?php

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use LaDanse\ServicesBundle\Service\DTO\Reference\GameClassReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\GameRaceReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\GuildReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

class GuildCharacter
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $level;

    /** @var GuildReference */
    protected $guildReference;

    /** @var RealmReference */
    protected $realmReference;

    /** @var GameClassReference */
    protected $gameClassReference;

    /** @var GameRaceReference */
    protected $gameRaceReference;

    /** @var Claim */
    protected $claim;

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
     * @return GuildReference
     */
    public function getGuildReference(): GuildReference
    {
        return $this->guildReference;
    }

    /**
     * @param GuildReference $guildReference
     * @return GuildCharacter
     */
    public function setGuildReference(GuildReference $guildReference): GuildCharacter
    {
        $this->guildReference = $guildReference;
        return $this;
    }

    /**
     * @return RealmReference
     */
    public function getRealmReference(): RealmReference
    {
        return $this->realmReference;
    }

    /**
     * @param RealmReference $realmReference
     * @return GuildCharacter
     */
    public function setRealmReference(RealmReference $realmReference): GuildCharacter
    {
        $this->realmReference = $realmReference;
        return $this;
    }

    /**
     * @return GameClassReference
     */
    public function getGameClassReference(): GameClassReference
    {
        return $this->gameClassReference;
    }

    /**
     * @param GameClassReference $gameClassReference
     * @return GuildCharacter
     */
    public function setGameClassReference(GameClassReference $gameClassReference): GuildCharacter
    {
        $this->gameClassReference = $gameClassReference;
        return $this;
    }

    /**
     * @return GameRaceReference
     */
    public function getGameRaceReference(): GameRaceReference
    {
        return $this->gameRaceReference;
    }

    /**
     * @param GameRaceReference $gameRaceReference
     * @return GuildCharacter
     */
    public function setGameRaceReference(GameRaceReference $gameRaceReference): GuildCharacter
    {
        $this->gameRaceReference = $gameRaceReference;
        return $this;
    }

    /**
     * @return Claim
     */
    public function getClaim(): Claim
    {
        return $this->claim;
    }

    /**
     * @param Claim $claim
     * @return GuildCharacter
     */
    public function setClaim(Claim $claim): GuildCharacter
    {
        $this->claim = $claim;
        return $this;
    }
}