<?php

namespace LaDanse\ServicesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\DomainBundle\Entity\Character,
    LaDanse\DomainBundle\Entity\CharacterVersion,
    LaDanse\DomainBundle\Entity\Claim,
    LaDanse\DomainBundle\Entity\PlaysRole,
    LaDanse\DomainBundle\Entity\Role,
    LaDanse\DomainBundle\Entity\Account;

class SettingsService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.SettingsService';

	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

    /*
     * Return for a specific account all known settings
     */
    public function getSettingsForAccount($accountId)
    {
    }

    /*
     * Return for a specific setting, the value (if it exists) for each account
     */
    public function getSettingForAllAccounts($settingName)
    {
    }

    /*
     * Update all settings passed for an account, settings not passed as
     * a parameter are left untouched.
     */
    public function updateSettingsForAccount($accountId, $settings)
    {
    }

    /*
     * Remove settings for the given account
     */
    public function removeSettingsForAccount($accountId, $settingNames)
    {
    }

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
}