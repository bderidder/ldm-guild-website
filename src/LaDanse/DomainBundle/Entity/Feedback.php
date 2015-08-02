<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Feedback")
 * @ORM\HasLifecycleCallbacks
 */
class Feedback
{
    const REPOSITORY = 'LaDanseDomainBundle:Feedback';

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $postedOn;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $feedback;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="postedBy", referencedColumnName="id", nullable=false)
     */
    protected $postedBy;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getPostedOn()
    {
        return $this->postedOn;
    }

    /**
     * @param \DateTime $postedOn
     */
    public function setPostedOn($postedOn)
    {
        $this->postedOn = $postedOn;
    }

    /**
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * @param string $feedback
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * @return Account
     */
    public function getPostedBy()
    {
        return $this->postedBy;
    }

    /**
     * @param Account $postedBy
     */
    public function setPostedBy($postedBy)
    {
        $this->postedBy = $postedBy;
    }
}
