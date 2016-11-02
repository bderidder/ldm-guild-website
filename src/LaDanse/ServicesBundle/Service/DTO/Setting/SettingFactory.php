<?php

namespace LaDanse\ServicesBundle\Service\DTO\Setting;

use LaDanse\DomainBundle\Entity as Entity;

class SettingFactory
{
    /**
     * @param $settings
     *
     * @return array
     */
    public static function create($settings)
    {
        $factory = new SettingFactory();

        return $factory->createSettings($settings);
    }

    protected function createSettings($settings)
    {
        $aggregate = [];

        /** @var Entity\Setting $setting */
        foreach($settings as $setting)
        {
            $aggregate[] = new Setting(
                $setting->getName(),
                $setting->getValue());
        }

        return $aggregate;
    }
}