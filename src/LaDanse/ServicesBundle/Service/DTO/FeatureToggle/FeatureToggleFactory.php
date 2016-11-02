<?php

namespace LaDanse\ServicesBundle\Service\DTO\FeatureToggle;

use LaDanse\DomainBundle\Entity as Entity;

class FeatureToggleFactory
{
    /**
     * @param $featureToggles
     *
     * @return array
     */
    public static function create($featureToggles)
    {
        $factory = new FeatureToggleFactory();

        return $factory->createFeatureToggles($featureToggles);
    }

    protected function createFeatureToggles($featureToggles)
    {
        $aggregate = [];

        /** @var Entity\FeatureToggle $featureToggle */
        foreach($featureToggles as $featureToggle)
        {
            $aggregate[] = new FeatureToggle(
                $featureToggle->getFeature(),
                $featureToggle->getToggle());
        }

        return $aggregate;
    }
}