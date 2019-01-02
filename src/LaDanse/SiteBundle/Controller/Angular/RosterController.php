<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\SiteBundle\Controller\Angular;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class RosterController extends LaDanseController
{
    /**
     * @return Response
     *
     * @Route("/roster", name="viewRoster")
     */
    public function viewAction()
    {
        return $this->render(
            'LaDanseSiteBundle:angular:angular.html.twig',
            [
                'pageTitle' => 'Roster'
            ]
        );
    }
}
