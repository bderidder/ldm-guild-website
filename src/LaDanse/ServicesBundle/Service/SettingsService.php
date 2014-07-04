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
    public function getSettingsForAccount($account)
    {
    }

    /*
     * Return for a specific setting, the value (if it exists) for each known account
     */
    public function getSettingForAllAccounts($settingName)
    {
    }

    /*
     * 
     */
    public function updateSettingsForAccount($account, $settings)
    {
    }

    public function removeSettingsForAccount($settingNames)
    {
    }
}