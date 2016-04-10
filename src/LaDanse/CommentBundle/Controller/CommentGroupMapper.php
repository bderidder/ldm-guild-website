<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommentBundle\Controller;

use LaDanse\DomainBundle\Entity\Comments\CommentGroup;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CommentGroupMapper
 *
 * @package LaDanse\CommentBundle\Controller
 */
class CommentGroupMapper
{
    /**
     * @param Controller $controller
     * @param CommentGroup $group
     *
     * @return object
     */
    public function mapGroup(Controller $controller, CommentGroup $group)
    {
        return (object)array(
            "groupId"   => $group->getId(),
            "createDate" => $group->getCreateDate()->format(\DateTime::ISO8601),
            "links"    => (object)array(
                "self"   => $controller->generateUrl('getCommentsInGroup', array('groupId' => $group->getId()), true)
            )
        );
    }

    /**
     * @param Controller $controller
     * @param CommentGroup $group
     *
     * @return object
     */
    public function mapGroupAndComments(Controller $controller, CommentGroup $group)
    {
        $groupObject = $this->mapGroup($controller, $group);

        $comments = $group->getComments()->getValues();

        usort(
            $comments,
            function ($a, $b) {
                /** @var $a \LaDanse\DomainBundle\Entity\Comments\Comment */
                /** @var $b \LaDanse\DomainBundle\Entity\Comments\COmment */

                return $a->getPostDate() < $b->getPostDate();
            }
        );

        $commentMapper = new CommentMapper();

        $jsonArray = array();

        foreach ($comments as $comment)
        {
            $jsonArray[] = $commentMapper->mapComment($controller, $comment);
        }

        $groupObject->comments = $jsonArray;

        return $groupObject;
    }
} 