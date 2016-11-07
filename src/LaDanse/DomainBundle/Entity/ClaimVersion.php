<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CharacterClaimVersion")
 */
class ClaimVersion extends VersionedEntity
{
    const REPOSITORY = 'LaDanseDomainBundle:ClaimVersion';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Claim")
     * @ORM\JoinColumn(name="claimId", referencedColumnName="id", nullable=false)
     */
    protected $claim;

    /**
     * @var string
     * @ORM\Column(type="text", length=1024, nullable=true)
     */
    protected $comment;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $raider = false;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getClaim()
    {
        return $this->claim;
    }

    /**
     * @param mixed $claim
     * @return $this
     */
    public function setClaim($claim)
    {
        $this->claim = $claim;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRaider()
    {
        return $this->raider;
    }

    /**
     * @param boolean $raider
     * @return $this
     */
    public function setRaider(bool $raider)
    {
        $this->raider = $raider;
        return $this;
    }
}
