<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect;

class VerificationReport
{
    /** @var bool */
    private $connected;

    /** @var bool */
    private $tokenValid;

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
     * @return VerificationReport
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTokenValid()
    {
        return $this->tokenValid;
    }

    /**
     * @param boolean $tokenValid
     * @return VerificationReport
     */
    public function setTokenValid($tokenValid)
    {
        $this->tokenValid = $tokenValid;
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
     * @return VerificationReport
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
     * @return VerificationReport
     */
    public function setCharactersLoaded($charactersLoaded)
    {
        $this->charactersLoaded = $charactersLoaded;
        return $this;
    }
}