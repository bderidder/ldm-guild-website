<?php

namespace LaDanse\SiteBundle\Controller\Feedback;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Feedback;
use LaDanse\SiteBundle\Form\Model\FeedbackFormModel;
use LaDanse\SiteBundle\Form\Type\FeedbackFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class FeedbackController extends LaDanseController
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
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/create", name="createFeedbackIndex")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $formModel = new FeedbackFormModel();
        $formModel->setDescription('');

        $form = $this->createForm(new FeedbackFormType(), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
           $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid())
            {
                $this->storeFeedback($authContext->getAccount(), $formModel->getDescription());

                $this->eventDispatcher->dispatch(
                    ActivityEvent::EVENT_NAME,
                    new ActivityEvent(
                        ActivityType::FEEDBACK_POST,
                        $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                        array(
                            'feedback' => $formModel->getDescription()
                        ))
                );

                return $this->render('LaDanseSiteBundle:feedback:feedbackResult.html.twig');
            }
            else
            {
                return $this->render('LaDanseSiteBundle:feedback:feedbackForm.html.twig',
                        array('form' => $form->createView(), 'errors' => $errors));
            }
        }
        else
        {
            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::FEEDBACK_VIEW,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount())
            );

            return $this->render('LaDanseSiteBundle:feedback:feedbackForm.html.twig',
                        array('form' => $form->createView()));
        }
    }

    private function storeFeedback($account, $description)
    {
        $em = $this->getDoctrine()->getManager();

        $feedback = new Feedback();

        $feedback->setPostedBy($account);
        $feedback->setPostedOn(new \DateTime());
        $feedback->setFeedback($description);

        $em->persist($feedback);

        $em->flush();
    }
}
