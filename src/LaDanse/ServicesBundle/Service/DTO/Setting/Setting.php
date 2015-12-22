<?php

namespace LaDanse\ServicesBundle\Service\DTO\Setting;

use JMS\Serializer\Annotation\SerializedName;

class Setting
{
    /**
     * @SerializedName("name")
     *
     * @var string
     */
    protected $name;

    /**
     * @SerializedName("value")
     *
     * @var string
     */
    protected $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}