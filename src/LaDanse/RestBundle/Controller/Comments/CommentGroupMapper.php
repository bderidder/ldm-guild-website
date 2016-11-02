<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Comments;

use LaDanse\DomainBundle\Entity\Comments\CommentGroup;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CommentGroupMapper
 *
 * @package LaDanse\CommentBundle\Controller
 */
class CommentGroupMapper
{
    /**
     * @param UrlGeneratorInterface $generator
     * @param CommentGroup $group
     *
     * @return object
     */
    public function mapGroup(UrlGeneratorInterface $generator, CommentGroup $group)
    {
        return (object)[
            "groupId"    => $group->getId(),
            "createDate" => $group->getCreateDate()->format(\DateTime::ISO8601),
            "links"      => (object)[
                "self" => $generator->generate('getCommentsInGroup', ['groupId' => $group->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param CommentGroup $group
     *
     * @return object
     */
    public function mapGroupAndComments(UrlGeneratorInterface $generator, CommentGroup $group)
    {
        $groupObject = $this->mapGroup($generator, $group);

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

        $jsonArray = [];

        foreach ($comments as $comment)
        {
            $jsonArray[] = $commentMapper->mapComment($generator, $comment);
        }

        $groupObject->comments = $jsonArray;

        return $groupObject;
    }
} 