<?php

namespace LaDanse\SiteBundle\Controller\Member;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use LaDanse\ServicesBundle\Service\Account\AccountService;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class ViewMemberController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @param string $accountId
     *
     * @return Response
     *
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

        /** @var GuildCharacterService $guildCharacterService */
        $guildCharacterService = $this->get(GuildCharacterService::SERVICE_NAME);

        $claimModel = (object)array(
            "accountId" => $accountId,
            "claims"    => $guildCharacterService->getClaimsForAccount($accountId)
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
