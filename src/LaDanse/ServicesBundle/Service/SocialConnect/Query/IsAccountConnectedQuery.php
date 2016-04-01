<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect\Query;

use LaDanse\CommonBundle\Helper\AbstractQuery;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SocialConnect;

/**
 * @DI\Service(IsAccountConnectedQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class IsAccountConnectedQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.IsAccountConnectedQuery';

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

        return count($socialConnects) == 1;
    }
}