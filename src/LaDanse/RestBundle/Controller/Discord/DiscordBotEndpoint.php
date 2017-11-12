<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Discord;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @Route("")
 */
class DiscordBotEndpoint extends AbstractRestController
{
    /**
     * @var \Psr\Log\LoggerInterface $logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/request", name="discordRequestsAction", options = { "expose" = false })
     * @Method({"GET"})
     */
    public function discordRequestsAction(Request $request)
    {
        $discordBotSecret = $this->container->getParameter('discord.bot.secret');

        $authzHeader = $request->headers->get('Authorization');

        if ($authzHeader !== "Bearer " . $discordBotSecret)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_FORBIDDEN,
                'Missing or invalid bearer token'
            );
        }

        $this->loginBot($request);

        try
        {
            /** @var EventService $eventService */
            $eventService = $this->get(EventService::SERVICE_NAME);

            $eventPage = $eventService->getAllEventsPaged(new \DateTime());

            return new JsonResponse($eventPage);
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    public function loginBot(Request $request)
    {
        $user = $this->getDoctrine()->getManager()->getRepository(Account::class)->findOneBy(array('username' => "DiscordBot"));

        $firewallName = 'main';

        $securityTokenStorage = $this->get('security.token_storage');
        $eventDispatcher = $this->get('event_dispatcher');

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());

        $securityTokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);

        $eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }
}
