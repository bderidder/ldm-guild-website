<?php

namespace LaDanse\SiteBundle\Controller\Roster;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use LaDanse\ServicesBundle\Service\Account\AccountService;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class RosterController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @return Response
     *
     * @Route("/", name="viewRoster")
     */
    public function viewAction()
    {
        /**
         * @var $accountService AccountService
         */
        $accountService = $this->get(AccountService::SERVICE_NAME);

        $accounts = $accountService->getAllActiveAccounts();

        return $this->render(
            'LaDanseSiteBundle:roster:viewRoster.html.twig',
            array(
                'accounts' => $accounts
            )
        );
    }
}
