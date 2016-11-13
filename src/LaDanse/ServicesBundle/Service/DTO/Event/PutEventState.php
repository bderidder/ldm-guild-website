<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

class PutEventState
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("state")
     * @Assert\NotBlank()
     */
    private $state;

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return PutEventState
     */
    public function setState(string $state): PutEventState
    {
        $this->state = $state;
        return $this;
    }
}