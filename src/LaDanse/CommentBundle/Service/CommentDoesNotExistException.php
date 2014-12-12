<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommentBundle\Service;

/**
 * Class CommentDoesNotExistException
 * @package LaDanse\ForumBundle\Service
 */
class CommentDoesNotExistException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}