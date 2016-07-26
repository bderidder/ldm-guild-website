<?php

namespace LaDanse\SiteBundle\Controller\Feedback;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Feedback\FeedbackService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\FeedbackFormModel;
use LaDanse\SiteBundle\Form\Type\FeedbackFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends LaDanseController
{
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
                /** @var FeedbackService $feedbackService */
                $feedbackService = $this->get(FeedbackService::SERVICE_NAME);

                $feedbackService->processFeedback($authContext->getAccount(), $formModel->getDescription());

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
}
