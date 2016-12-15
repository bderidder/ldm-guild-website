<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use DateInterval;
use Eluceo\iCal\Component as iCal;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\CalendarExport;
use LaDanse\DomainBundle\Entity\Comments\Comment;
use LaDanse\ServicesBundle\Activity\ActivityEvent;

use LaDanse\ServicesBundle\Activity\ActivityType;

use LaDanse\ServicesBundle\Service\Comments\CommentService;
use LaDanse\ServicesBundle\Service\Event\EventService;

use LaDanse\ServicesBundle\Service\Settings\SettingsService;

use LaDanse\SiteBundle\Common\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class ICalController extends LaDanseController
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
     * @param $secret string
     *
     * @return Response
     *
     * @Route("/ical/{secret}", name="icalIndex")
     */
    public function indexAction(Request $request, $secret)
    {
        $exportSettings = $this->getExportSettings($secret);

        if ($exportSettings === null)
        {
            $this->logger->info(__CLASS__ . " Supplied secret is not known", ["secret" => $secret]);

            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->logger->info(
            __CLASS__ . " found account for secret ",
            [
                "account" => $exportSettings->getAccount()->getUsername()
            ]
        );

        $account = $exportSettings->getAccount();

        $this->loginAccount($request, $account);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CALENDAR_ICAL,
                $account
            )
        );

        $vCalendar = new iCal\Calendar('www.ladanse.org');

        // we suggest a refresh every 30 minutes
        $vCalendar->setPublishedTTL('PT30M');

        $allEvents = $this->getEvents($account);

        /** @var \LaDanse\SiteBundle\Model\EventModel $event */
        foreach($allEvents as $event)
        {
            if ($event->getSignUps()->getCurrentUserWillCome() || $event->getSignUps()->getCurrentUserMightCome())
            {
                $vEvent = $this->createICalEvent($event, '(SIGNED) ' . $event->getName());
                $vEvent->setStatus(iCal\Event::STATUS_CONFIRMED);

                $vCalendar->addComponent($vEvent);
            }

            if ($event->getSignUps()->getCurrentUserAbsent() && $exportSettings->getExportAbsence())
            {
                $vEvent = $this->createICalEvent($event, '(ABSENT) ' . $event->getName());
                $vEvent->setStatus(iCal\Event::STATUS_TENTATIVE); // CANCELLED is not always shown in some applications

                $vCalendar->addComponent($vEvent);
            }

            if (!($event->getSignUps()->getCurrentUserSignedUp() || $event->getSignUps()->getCurrentUserAbsent())
                && $exportSettings->getExportNew())
            {
                $vEvent = $this->createICalEvent($event, '(NEW) ' . $event->getName());
                $vEvent->setStatus(iCal\Event::STATUS_TENTATIVE);

                $vCalendar->addComponent($vEvent);
            }
        }

        return new Response(
            $vCalendar->render(),
            Response::HTTP_OK,
            [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="cal.ics"'
            ]
        );
    }

    protected function getEvents(Account $currentUser)
    {
        $today = new \DateTime();

        $pageStartDate = clone $today;
        $pageStartDate->sub(new DateInterval('P56D'));
        $pageStartDate->setTime(0, 0, 0);

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $eventModels = [];

        for($i = 0; $i < 4; $i++)
        {
            $eventPage = $eventService->getAllEventsPaged($pageStartDate);

            foreach($eventPage->getEvents() as $event)
            {
                $eventModels[] = new EventModel($event, $currentUser);
            }

            $pageStartDate = $eventPage->getNextTimestamp();
        }

        return $eventModels;
    }

    /**
     * @param $secret string
     *
     * @return CalendarExport
     */
    protected function getExportSettings($secret)
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        return $settingsService->findCalendarExportBySecret($secret);
    }

    /**
     * @param $event \LaDanse\SiteBundle\Model\EventModel
     * @param $description string
     *
     * @return iCal\Event
     */
    protected function createICalEvent($event, $description)
    {
        $vEvent = new iCal\Event();
        $vEvent->setSequence(floor(microtime(true)));
        $vEvent->setUniqueId($event->getId());
        $vEvent->setDtStart($event->getInviteTime());
        $vEvent->setDtEnd($event->getEndTime());
        $vEvent->setSummary($description);
        $vEvent->setDescription($this->createDescription($event));

        $vEvent->setUrl($this->generateUrl('viewEvent', ['id' => $event->getId()], true));

        $vEvent->setUseTimezone(true);

        return $vEvent;
    }

    /**
     * @param EventModel $event
     *
     * @return string
     */
    protected function createDescription(EventModel $event)
    {
        $description = "";

        /**
         * @var CommentService $commentService
         */
        $commentService = $this->get(CommentService::SERVICE_NAME);

        $commentGroup = $commentService->getCommentGroup($event->getTopicId());

        $comments = $commentGroup->getComments()->getValues();

        usort(
            $comments,
            function ($a, $b) {
                /** @var $a \LaDanse\DomainBundle\Entity\Comments\Comment */
                /** @var $b \LaDanse\DomainBundle\Entity\Comments\COmment */

                return $a->getPostDate() < $b->getPostDate();
            }
        );

        $description = $description . $event->getDescription();

        if (($event->getDescription() !== null || strlen($event->getDescription()) > 0)
            &&
            ($commentGroup->getComments()->count() > 0))
        {
            $description = $description . "\n\n";
        }

        for($i = 0; $i < count($comments); $i++)
        {
            /**
             * @var Comment $comment
             */
            $comment = $comments[$i];

            if ($i >= 1)
            {
                $description = $description . "\n\n";
            }

            $description = $description . $comment->getPoster()->getDisplayName();
            $description = $description . " (" . $comment->getPostDate()->format("d/m H:i") . ") ";
            $description = $description . $comment->getMessage();
        }

        if (($event->getDescription() !== null || strlen($event->getDescription()) > 0)
            ||
            ($commentGroup->getComments()->count() > 0))
        {
            $description = $description . "\n\n";
            $description = $description . $this->generateUrl('viewEvent', ['id' => $event->getId()], true);
        }

        return $description;
    }

    private function loginAccount(Request $request, Account $account)
    {
        $token = new UsernamePasswordToken($account, null, "main", $account->getRoles());
        $this->get("security.token_storage")->setToken($token); //now the user is logged in

        //now dispatch the login event
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }
}
