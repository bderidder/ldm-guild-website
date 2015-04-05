<?php

namespace LaDanse\SiteBundle\Controller\Member;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\AccountService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class ViewMemberController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @Route("/{accountId}", name="viewMember")
     */
    public function viewAction($accountId)
    {
        /**
         * @var $accountService AccountService
         */
        $accountService = $this->get(AccountService::SERVICE_NAME);

        $account = $accountService->getAccount($accountId);

        if ($account == null)
        {
            return $this->redirect($this->generateUrl('menuIndex'));
        }

        $claimModel = (object)array(
            "accountId" => $accountId,
            "claims"    => $this->getGuildCharacterService()->getClaimsForAccount($accountId)
        );

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::MEMBER_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:member:viewMember.html.twig',
            array(
                'member' => $account,
                'claimModel' => $claimModel
            )
        );
    }
}
