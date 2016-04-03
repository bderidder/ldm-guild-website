<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect\Command;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\CommonBundle\Helper\AbstractCommand;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SocialConnect;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(DisconnectAccountCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class DisconnectAccountCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.DisconnectAccountCommand';
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var Account $account */
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

    protected function runCommand()
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $this->account));

        if (count($socialConnects) == 1)
        {
            $this->doctrine->getManager()->remove($socialConnects[0]);
            $this->doctrine->getManager()->flush();
        }
    }
}