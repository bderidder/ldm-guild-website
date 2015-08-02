<?php

namespace LaDanse\ServicesBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Setting;
use Symfony\Component\DependencyInjection\ContainerInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class SettingsService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(SettingsService::SERVICE_NAME, public=true)
 */
class SettingsService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.SettingsService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
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

        /** @var Setting $setting */
        foreach($settings as $setting)
        {
            $settingModels[$setting->getName()] = $this->settingToDto($setting);
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
     * @param Account $account
     * @param array $settings
     */
    public function updateSettingsForAccount(Account $account, $settings)
    {
        $em = $this->getDoctrine()->getManager();

        $clonedSettings = $settings;

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:selectSettingsForAccount.sql.twig')
        );
        $query->setParameter('accountId', $account->getId());
        $query->setParameter('namePattern', '%');

        $currentSettings = $query->getResult();

        for($i = 0; $i < count($clonedSettings); $i++)
        {
            /** @var mixed $setting */
            $setting = $clonedSettings[$i];

            /** @var Setting $currentSetting */
            foreach($currentSettings as $currentSetting)
            {
                if ($currentSetting->getName() == $setting->name)
                {
                    $currentSetting->setValue($setting->value);

                    $clonedSettings[$i] = null;
                }
            }
        }

        for($i = 0; $i < count($clonedSettings); $i++)
        {
            /** @var mixed $setting */
            $setting = $clonedSettings[$i];

            if (!is_null($setting))
            {
                $newSetting = new Setting();

                $newSetting->setAccount($account);
                $newSetting->setName($setting->name);
                $newSetting->setValue($setting->value);

                $em->persist($newSetting);
            }
        }

        $em->flush();
    }

    /**
     * Remove settings for the given account
     *
     * @param $accountId
     * @param string $settingNames
     */
    public function removeSettingForAccount($accountId, $settingNames = '')
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
            'account'   => $setting->getAccount()
        );
    }
}