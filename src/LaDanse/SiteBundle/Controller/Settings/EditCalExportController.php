<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;
use LaDanse\SiteBundle\Form\Model\CalExportFormModel;
use LaDanse\SiteBundle\Form\Type\CalExportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class EditCalExportController extends LaDanseController
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
     * @Route("/calExport", name="editCalExport")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in editProfile');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $account = $authContext->getAccount();

        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        $calExport = $settingsService->findCalendarExportByAccount($account);

        $formModel = new CalExportFormModel();

        $formModel->setExportSignUp(true);
        $formModel->setExportNew($calExport->getExportNew());
        $formModel->setExportAbsence($calExport->getExportAbsence());

        $form = $this->createForm(new CalExportFormType(), $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $settingsService->updateCalendarExport(
                    $account,
                    $formModel->isExportAbsence(),
                    $formModel->isExportNew()
                );

               return $this->redirect($this->generateUrl('editCalExport'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:settings:editCalExport.html.twig',
                    array(
                        'form' => $form->createView(),
                        'personalUrl' => $this->generateUrl('icalIndex', array('secret' => $calExport->getSecret()), true)
                    )
                );
            }
        }
        else
        {
            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::SETTINGS_CALEXPORT_VIEW,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount())
            );

            return $this->render('LaDanseSiteBundle:settings:editCalExport.html.twig',
                array(
                    'form' => $form->createView(),
                    'personalUrl' => $this->generateUrl('icalIndex', array('secret' => $calExport->getSecret()), true)
                )
            );
        }
    }

    /**
     * @return Response
     *
     * @Route("/resetSecret", name="resetSecret")
     */
    public function resetSecret()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in editProfile');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $account = $authContext->getAccount();

        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        $settingsService->resetCalendarExportSecret($account);

        return $this->redirect($this->generateUrl('editCalExport'));
    }
}
