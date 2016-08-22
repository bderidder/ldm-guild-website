<?php
/**
 * Created by PhpStorm.
 * User: bavo
 * Date: 21/08/16
 * Time: 17:40
 */

namespace LaDanse\ServicesBundle\Service\DTO\Reference;


class GuildReference
{
    /** @var string */
    private $guildId;

    /**
     * @return string
     */
    public function getGuildId(): string
    {
        return $this->guildId;
    }

    /**
     * @param string $guildId
     * @return GuildReference
     */
    public function setGuildId(string $guildId): GuildReference
    {
        $this->guildId = $guildId;
        return $this;
    }
}