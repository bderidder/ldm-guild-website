<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Character\CharacterService;

use LaDanse\ServicesBundle\Service\SocialConnect\SocialConnectService;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class ViewClaimsController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @var SocialConnectService $socialConnectService
     * @DI\Inject(SocialConnectService::SERVICE_NAME)
     */
    private $socialConnectService;

    /**
     * @Route("/", name="viewClaims")
     *
     * @return Response
     */
    public function viewAction()
    {
        /** @var CharacterService $guildCharacterService */
        $guildCharacterService = $this->get(CharacterService::SERVICE_NAME);
        
        $account = $this->getAccount();
        $accountId = $account->getId();

        $claimModel = (object)array(
            "accountId" => $accountId,
            "claims"    => $guildCharacterService->getClaimsForAccount($accountId)
        );

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:claims:viewClaims.html.twig',
            array(
                'claimModel'  => $claimModel,
                'isConnected' => $this->socialConnectService->isAccountConnected($account)
            )
        );
    }
}
