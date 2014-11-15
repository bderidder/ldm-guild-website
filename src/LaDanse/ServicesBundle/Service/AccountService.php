<?php

namespace LaDanse\ServicesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\DomainBundle\Entity\Account;

class AccountService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.AccountService';

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
    public function isLoginUsed($login, $exceptAccountId = 9999999)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle:settings:isLoginUsed.sql.twig'));
        $query->setParameter('accountId', $exceptAccountId);
        $query->setParameter('login', $login);

        $result = $query->getResult();

        return !(count($result) == 0);
    }

    /*
     * Check if a given display name is already used by an account except the given account
     */
    public function isDisplayNameUsed($displayName, $exceptAccountId = 9999999)
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
    public function isEmailUsed($email, $exceptAccountId = 9999999)
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
}