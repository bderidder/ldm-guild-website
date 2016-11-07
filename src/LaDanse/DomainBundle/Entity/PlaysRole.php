<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="PlaysRole")
 */
class PlaysRole extends VersionedEntity
{
    const REPOSITORY = 'LaDanseDomainBundle:PlaysRole';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Claim", inversedBy="roles")
     * @ORM\JoinColumn(name="claimId", referencedColumnName="id", nullable=false)
     */
    protected $claim;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return PlaysRole
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set claim
     *
     * @param Claim $claim
     * @return PlaysRole
     */
    public function setClaim(Claim $claim)
    {
        $this->claim = $claim;

        return $this;
    }

    /**
     * Get claim
     *
     * @return Claim
     */
    public function getClaim()
    {
        return $this->claim;
    }

    /**
     * @param $roleStr
     * @return bool
     */
    public function isRole($roleStr)
    {
        return strcmp($roleStr, $this->role) == 0;
    }
}
