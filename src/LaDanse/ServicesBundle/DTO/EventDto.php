<?php

namespace LaDanse\ServicesBundle\DTO\Events;

class EventDto
{
    /** @var string */
    private $name;
    /** @var string */
    private $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return EventDto
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EventDto
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}