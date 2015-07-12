<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/{id}/signup")
*/
class RemoveSignUpController extends LaDanseController
{
    const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

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
     * @param $id string
     *
     * @return Response
     *
     * @Route("/remove", name="removeSignUp")
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }

        /** @var $signUp SignUp */
        $signUp = $this->getCurrentUserSignUp($event);

        /** @var $eventService EventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $eventService->removeSignUp($signUp->getId());

        $this->addToast('Sign up removed');

        return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
    }

    private function getCurrentUserSignUp(Event $event)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT s ' .
                                  'FROM LaDanse\DomainBundle\Entity\SignUp s ' . 
                                  'WHERE s.event = :event AND s.account = :account');
        $query->setParameter('account', $account->getId());
        $query->setParameter('event', $event->getId());

        $signUps = $query->getResult();

        if (count($signUps) === 0)
        {
            return NULL;
        }
        else
        {
            return $signUps[0];
        }
    }
}
