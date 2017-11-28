<?php

namespace LaDanse\ServicesBundle\Service\DTO\Discord;


use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @ExclusionPolicy("none")
 */
class AccessTokenGrant
{
    /**
     * @var string
     * @SerializedName("nonce")
     */
    private $nonce;

    /**
     * @var string
     * @SerializedName("accessToken")
     */
    private $accessToken;

    /**
     * @var int
     * @SerializedName("issuedOn")
     */
    private $issuedOn;

    /**
     * @return string
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * @param string $nonce
     * @return AccessTokenGrant
     */
    public function setNonce(string $nonce): AccessTokenGrant
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return AccessTokenGrant
     */
    public function setAccessToken(string $accessToken): AccessTokenGrant
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssuedOn(): int
    {
        return $this->issuedOn;
    }

    /**
     * @param int $issuedOn
     * @return AccessTokenGrant
     */
    public function setIssuedOn(int $issuedOn): AccessTokenGrant
    {
        $this->issuedOn = $issuedOn;
        return $this;
    }
}