<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;

class ViewClaimsController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @Route("/", name="viewClaims")
     *
     * @return Response
     */
    public function viewAction()
    {
        $accountId = $this->getAccount()->getId();

        $claimModel = (object)array(
            "accountId" => $accountId,
            "claims"    => $this->getGuildCharacterService()->getClaimsForAccount($accountId)
        );

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:claims:viewClaims.html.twig',
            array('claimModel' => $claimModel)
        );
    }
}
