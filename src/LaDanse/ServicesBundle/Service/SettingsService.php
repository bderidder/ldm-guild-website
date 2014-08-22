<?php

namespace LaDanse\ServicesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\DomainBundle\Entity\Account;

class SettingsService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.SettingsService';

	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

    public function updateProfile($accountId, $displayName, $email)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Account::REPOSITORY);

        $account = $repo->find($accountId);

        $account->setDisplayName($displayName);
        $account->setEmail($email);

        $em->flush();
    }

    /*
     * Check if a given display name is already used by an account except the given account
     */
    public function isDisplayNameUsed($displayName, $exceptAccountId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:isDisplayNameUsed.sql.twig'));
        $query->setParameter('accountId', $exceptAccountId);
        $query->setParameter('displayName', $displayName);

        $result = $query->getResult();

        return !(count($result) == 0);
    }

    /*
     * Check if a given email is already used by an account except the given account
     */
    public function isEmailUsed($email, $exceptAccountId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:isEmailUsed.sql.twig'));
        $query->setParameter('accountId', $exceptAccountId);
        $query->setParameter('email', $email);

        $result = $query->getResult();

        return !(count($result) == 0);
    }

    /*
     * Return for a specific account all known settings
     */
    public function getSettingsForAccount($accountId, $settingNamePrefix = '')
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:selectSettingsForAccount.sql.twig'));
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

    /*
     * Return for a specific setting (prefix), the value (if it exists) for each account
     */
    public function getSettingsForAllAccounts($settingNamePrefix)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:selectSettingsForAllAccounts.sql.twig'));
        $query->setParameter('namePattern', $settingNamePrefix . '%');
        
        $settings = $query->getResult();

        $settingModels = array();

        foreach($settings as $setting)
        {
            $settingModels[] = $this->settingToDto($setting);
        }

        return $settingModels;
    }

    /*
     * Update all settings passed for an account, settings not passed as
     * a parameter are left untouched.
     * Settings that didn't exist yet are created on the fly.
     */
    public function updateSettingsForAccount($accountId, $settings)
    {
    }

    /*
     * Remove settings for the given account
     */
    public function removeSettingsForAccount($accountId, $settingNames = '')
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