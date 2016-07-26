<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SocialConnect;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(GetAccessTokenForAccountQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class GetAccessTokenForAccountQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAccessTokenForAccountQuery';

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

    /** @var Account */
    private $account;

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
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    protected function validateInput()
    {
        return ($this->account != null);
    }

    protected function runQuery()
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $this->account));

        if (count($socialConnects) == 1)
        {
            return $socialConnects[0]->getAccessToken();
        }

        return null;
    }
}