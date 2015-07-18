<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FeatureToggle")
 */
class FeatureToggle
{
    const REPOSITORY = 'LaDanseDomainBundle:FeatureToggle';

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $feature;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $toggle;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="toggleFor", referencedColumnName="id", nullable=true)
     */
    protected $toggleFor;

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
     * @return string
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param string $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return boolean
     */
    public function getToggle()
    {
        return $this->toggle;
    }

    /**
     * @param boolean $toggle
     */
    public function setToggle($toggle)
    {
        $this->toggle = $toggle;
    }

    /**
     * @return Account
     */
    public function getToggleFor()
    {
        return $this->toggleFor;
    }

    /**
     * @param Account $toggleFor
     */
    public function setToggleFor($toggleFor)
    {
        $this->toggleFor = $toggleFor;
    }
}
