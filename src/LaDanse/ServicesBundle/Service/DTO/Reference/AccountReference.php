<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

use JMS\Serializer\Annotation\SerializedName;

class AccountReference
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * @SerializedName("name")
     *
     * @var string
     */
    protected $name;

    /**
     * AccountReference constructor.
     *
     * @param int $id
     * @param string $name
     */
    public function __construct($id,
                                $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}