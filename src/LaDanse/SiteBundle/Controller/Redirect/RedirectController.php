<?php

namespace LaDanse\SiteBundle\Controller\Redirect;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\Response;

class RedirectController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

	/**
     * @param string $url URL to redirect to
     *
     * @return Response
     *
     * @Route("/{url}", name="redirect", requirements={"url"=".+"})
     */
    public function indexAction($url)
    {
        if ($this->getAuthenticationService()->getCurrentContext()->isAuthenticated())
        {
            $account = $this->getAuthenticationService()->getCurrentContext()->getAccount();
        }
        else
        {
            $account = null;
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::REDIRECT,
                $account,
                array(
                    'url' => $url
                )
            )
        );

        return $this->redirect($url);
    }
}
