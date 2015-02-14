<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\EventListener\Features;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

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
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

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

        $signUp = $this->getCurrentUserSignUp($event);

        $em->remove($signUp);
        $em->flush();

        $this->eventDispatcher->dispatch(
            FeatureUseEvent::EVENT_NAME,
            new FeatureUseEvent(
                Features::SIGNUP_DELETE,
                $this->getAuthenticationService()->getCurrentContext()->getAccount()
            )
        );

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
