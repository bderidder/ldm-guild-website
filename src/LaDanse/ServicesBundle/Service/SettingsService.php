<?php

namespace LaDanse\ServicesBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SettingsService
 * @package LaDanse\ServicesBundle\Service
 */
class SettingsService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.SettingsService';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Return for a specific account all known settings
     *
     * @param $accountId
     * @param string $settingNamePrefix
     * @return array
     */
    public function getSettingsForAccount($accountId, $settingNamePrefix = '')
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:selectSettingsForAccount.sql.twig')
        );
        $query->setParameter('accountId', $accountId);
        $query->setParameter('namePattern', $settingNamePrefix . '%');
        
        $settings = $query->getResult();

        $settingModels = array();

        foreach($settings as $setting)
        {
            $settingModels[] = $this->settingToDto($setting);
        }

        return $settingModels;
    }

    /**
     * Return for a specific setting (prefix), the value (if it exists) for each account
     *
     * @param $settingNamePrefix
     * @return array
     */
    public function getSettingsForAllAccounts($settingNamePrefix)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:selectSettingsForAllAccounts.sql.twig')
        );
        $query->setParameter('namePattern', $settingNamePrefix . '%');
        
        $settings = $query->getResult();

        $settingModels = array();

        foreach($settings as $setting)
        {
            $settingModels[] = $this->settingToDto($setting);
        }

        return $settingModels;
    }

    /**
     * Update all settings passed for an account, settings not passed as
     * a parameter are left untouched.
     * Settings that didn't exist yet are created on the fly.
     *
     * @param $accountId
     * @param $settings
     */
    public function updateSettingsForAccount($accountId, $settings)
    {
    }

    /**
     * Remove settings for the given account
     *
     * @param $accountId
     * @param string $settingNames
     */
    public function removeSettingsForAccount($accountId, $settingNames = '')
    {
    }

    /**
     * @param $settings
     * @param $settingName
     * @return bool
     */
    protected function doesSettingExist($settings, $settingName)
    {
        foreach($settings as $setting)
        {
            if ($setting->name == $settingName)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $setting \LaDanse\DomainBundle\Entity\Setting
     * @return object
     */
    protected function settingToDto($setting)
    {
        return (object) array(
            'name'      => $setting->getName(),
            'value'     => $setting->getValue(),
            'accountId' => $setting->getAccount()->getId()
        );
    }
}