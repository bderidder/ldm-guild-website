<?php

namespace LaDanse\ServicesBundle\Service\DTO\Discord;


use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @ExclusionPolicy("none")
 */
class DiscordConnectStatus
{
    /**
     * @var bool
     * @SerializedName("connected")
     */
    private $connected;

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * @param bool $connected
     */
    public function setConnected(bool $connected)
    {
        $this->connected = $connected;
    }
}