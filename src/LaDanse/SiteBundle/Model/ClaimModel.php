<?php

namespace LaDanse\SiteBundle\Model;

class ClaimModel
{
    protected $characterName;
    protected $startTime;
    protected $playsTank;
    protected $playsHealer;
    protected $playsDPS;
    
    public function __construct($claimsDto)
    {
        $this->characterName = $claimsDto->character;
        $this->playsTank = $claimsDto->playsTank;
        $this->playsHealer = $claimsDto->playsHealer;
        $this->playsDPS = $claimsDto->playsDPS;
    }

    public function getCharacterName()
    {
        return $this->characterName;
    }

    public function setCharacterName($characterName) 
    {

        $this->characterName = $characterName;
    
        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime) 
    {
        $this->startTime = $startTime;
    
        return $this;
    }

    public function getPlaysTank()
    {
        return $this->playsTank;
    }
 
    public function setPlaysTank($playsTank) 
    {
        $this->playsTank = $playsTank;
    
        return $this;
    }

    public function getPlaysHealer()
    {
        return $this->playsHealer;
    }

    public function setPlaysHealer($playsHealer) 
    {
        $this->playsHealer = $playsHealer;
    
        return $this;
    }

    public function getPlaysDPS()
    {
        return $this->playsDPS;
    }

    public function setPlaysDPS($playsDPS) 
    {
        $this->playsDPS = $playsDPS;
    
        return $this;
    }
}
