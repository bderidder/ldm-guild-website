<?php

namespace LaDanse\RestBundle\Controller\Profile;


use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Discord\DiscordConnectService;
use LaDanse\ServicesBundle\Service\DTO\Profile\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("")
 */
class ProfileResource extends AbstractRestController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("", name="profile", options = { "expose" = true })
     * @Method({"GET","HEAD"})
     */
    public function profileAction(Request $request)
    {
        /** @var Account $account */
        $account = $this->getAccount();

        $profile = new Profile();

        $profile->setId($account->getId());
        $profile->setDisplayName($account->getDisplayName());

        return new JsonResponse(ResourceHelper::object($profile));
    }
}
