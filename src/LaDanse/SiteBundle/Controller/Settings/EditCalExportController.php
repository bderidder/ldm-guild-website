<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\CalExportFormModel;
use LaDanse\SiteBundle\Form\Type\CalExportFormType;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $account = $authContext->getAccount();

        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        $calExport = $settingsService->findCalendarExportByAccount($account);

        $formModel = new CalExportFormModel();

        $formModel->setExportSignUp(true);
        $formModel->setExportNew($calExport->getExportNew());
        $formModel->setExportAbsence($calExport->getExportAbsence());

        $form = $this->createForm(CalExportFormType::class, $formModel,
            ['attr' => ['class' => 'form-horizontal', 'novalidate' => '']]);

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
                    [
                        'form' => $form->createView(),
                        'personalUrl' => $this->generateUrl('icalIndex', ['secret' => $calExport->getSecret()], UrlGeneratorInterface::ABSOLUTE_URL)
                    ]
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
                [
                    'form' => $form->createView(),
                    'personalUrl' => $this->generateUrl('icalIndex', ['secret' => $calExport->getSecret()], UrlGeneratorInterface::ABSOLUTE_URL)
                ]
            );
        }
    }

    /**
     * @return Response
     *
     * @Route("/resetSecret", name="resetSecret")
     */
    public function resetSecretAction()
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
