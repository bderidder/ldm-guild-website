<?php


namespace LaDanse\ServicesBundle\Command\Armory;


use Exception;

class GuildWrapper extends APIObjectWrapper
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
    public function getGuildName(): string
    {
        return $this->getPropertyValue2('guild', 'name');
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getGuildGameId(): int
    {
        return $this->getPropertyValue2('guild', 'id');
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getRealmName(): string
    {
        return $this->getPropertyValue3('guild', 'realm', 'name');
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getRealmGameId(): int
    {
        return $this->getPropertyValue3('guild', 'realm', 'id');
    }

    /**
     * @return object
     *
     * @throws Exception
     */
    public function getMembers()
    {
        return $this->getPropertyValue1('members');
    }
}