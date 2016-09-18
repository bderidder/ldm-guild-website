<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\SiteBundle\Controller\Characters;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Forum\ForumStatsService;

use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CharactersController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @Route("/", name="charactersIndex")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in charactersIndex');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }


        return $this->render("LaDanseSiteBundle:characters:characters.html.twig");
    }
}
