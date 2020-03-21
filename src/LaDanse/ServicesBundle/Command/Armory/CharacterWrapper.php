<?php


namespace LaDanse\ServicesBundle\Command\Armory;


use Exception;

class CharacterWrapper extends APIObjectWrapper
{
    public function __construct($record)
    {
        parent::__construct($record);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getName(): string
    {
        return $this->getPropertyValue2('character', 'name');
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getLevel(): int
    {
        return $this->getPropertyValue2('character', 'level');
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getClassGameId(): int
    {
        return $this->getPropertyValue3('character', 'playable_class', 'id');
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getRaceGameId(): int
    {
        return $this->getPropertyValue3('character', 'playable_race', 'id');
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getRealmGameId(): int
    {
        return $this->getPropertyValue3('character', 'realm', 'id');
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getRealmSlug(): string
    {
        return $this->getPropertyValue3('character', 'realm', 'slug');
    }
}