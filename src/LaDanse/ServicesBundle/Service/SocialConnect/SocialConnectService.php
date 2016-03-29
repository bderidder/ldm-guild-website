<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\MailSend;
use LaDanse\DomainBundle\Entity\SocialConnect;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trt\SwiftCssInlinerBundle\Plugin\CssInlinerPlugin;

/**
 * @DI\Service(SocialConnectService::SERVICE_NAME, public=true)
 */
class SocialConnectService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.SocialConnectService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

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
     * @param Account $account
     *
     * @return bool
     */
    public function isAccountConnected(Account $account)
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $account));

        return count($socialConnects) == 1;
    }

    /**
     * @param Account $account
     */
    public function disconnectAccount(Account $account)
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $account));

        if (count($socialConnects) == 1)
        {
            $this->doctrine->getManager()->remove($socialConnects[0]);
            $this->doctrine->getManager()->flush();
        }
    }

    /**
     * @param Account $account
     *
     * @return string
     */
    public function getAccessTokenForAccount(Account $account)
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $account));

        if (count($socialConnects) == 1)
        {
            return $socialConnects[0]->getAccessToken();
        }

        return null;
    }

    /**
     * @param Account $account
     *
     * @return bool
     */
    public function verifyAccountConnection(Account $account)
    {
        return false;
    }
}