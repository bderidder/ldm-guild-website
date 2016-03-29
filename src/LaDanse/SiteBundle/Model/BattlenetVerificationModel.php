<?php

namespace LaDanse\SiteBundle\Model;

class BattlenetVerificationModel
{
    /** @var bool */
    private $connected;

    /** @var bool */
    private $checkAccessToken;

    /** @var bool */
    private $accessTokenExpired;

    /** @var \DateTime */
    private $expirationDate;

    /** @var bool */
    private $charactersLoaded;

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param boolean $connected
     * @return BattlenetVerificationModel
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCheckAccessToken()
    {
        return $this->checkAccessToken;
    }

    /**
     * @param boolean $checkAccessToken
     * @return BattlenetVerificationModel
     */
    public function setCheckAccessToken($checkAccessToken)
    {
        $this->checkAccessToken = $checkAccessToken;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAccessTokenExpired()
    {
        return $this->accessTokenExpired;
    }

    /**
     * @param boolean $accessTokenExpired
     * @return BattlenetVerificationModel
     */
    public function setAccessTokenExpired($accessTokenExpired)
    {
        $this->accessTokenExpired = $accessTokenExpired;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     * @return BattlenetVerificationModel
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCharactersLoaded()
    {
        return $this->charactersLoaded;
    }

    /**
     * @param boolean $charactersLoaded
     * @return BattlenetVerificationModel
     */
    public function setCharactersLoaded($charactersLoaded)
    {
        $this->charactersLoaded = $charactersLoaded;
        return $this;
    }
}