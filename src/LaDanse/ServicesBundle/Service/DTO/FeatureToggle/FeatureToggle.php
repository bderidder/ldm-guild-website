<?php

namespace LaDanse\ServicesBundle\Service\DTO\FeatureToggle;

use JMS\Serializer\Annotation\SerializedName;

class FeatureToggle
{
    /**
     * @SerializedName("feature")
     *
     * @var string
     */
    protected $feature;

    /**
     * @SerializedName("toggle")
     *
     * @var boolean
     */
    protected $toggle;

    public function __construct($feature, $toggle)
    {
        $this->feature = $feature;
        $this->toggle = $toggle;
    }

    /**
     * @return string
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @return boolean
     */
    public function isToggle()
    {
        return $this->toggle;
    }
}