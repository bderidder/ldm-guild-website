<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\EventListener\Features;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

use JMS\DiExtraBundle\Annotation as DI;

class ViewClaimsController extends LaDanseController
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
     * @Route("/", name="viewClaims")
     */
    public function viewAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in viewClaims');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $accountId = $authContext->getAccount()->getId();

        $claimModel = (object)array(
            "accountId" => $accountId,
            "claims"    => $this->getGuildCharacterService()->getClaims($accountId)
        );

        $this->eventDispatcher->dispatch(
            FeatureUseEvent::EVENT_NAME,
            new FeatureUseEvent(
                Features::CLAIM_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount()
            )
        );

        return $this->render(
            'LaDanseSiteBundle:claims:viewClaims.html.twig',
            array('claimModel' => $claimModel)
        );
    }
}
