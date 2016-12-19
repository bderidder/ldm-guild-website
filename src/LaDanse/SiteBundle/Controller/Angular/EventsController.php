<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\SiteBundle\Controller\Angular;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class EventsController extends LaDanseController
{
    /**
     * @Route("/events", name="eventsIndex")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render(
            'LaDanseSiteBundle:angular:angular.html.twig',
            [
                'pageTitle' => 'Calendar'
            ]
        );
    }
}
