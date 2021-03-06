<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FeatureUse")
 * @ORM\HasLifecycleCallbacks
 */
class FeatureUse
{
    const REPOSITORY = 'LaDanseDomainBundle:FeatureUse';

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
    protected $usedOn;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $feature;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $rawData;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="usedBy", referencedColumnName="id", nullable=true)
     */
    protected $usedBy;

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
    public function getUsedOn()
    {
        return $this->usedOn;
    }

    /**
     * @param \DateTime $usedOn
     */
    public function setUsedOn($usedOn)
    {
        $this->usedOn = $usedOn;
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
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param string $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return Account
     */
    public function getUsedBy()
    {
        return $this->usedBy;
    }

    /**
     * @param Account $usedBy
     */
    public function setUsedBy($usedBy)
    {
        $this->usedBy = $usedBy;
    }

    /**
     * @return string
     */
    public function getData()
    {
        if ($this->rawData == NULL)
        {
            return NULL;
        }

        return json_decode($this->rawData);
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        if ($data == NULL)
        {
            $this->rawData = NULL;
        }
        else
        {
            $this->rawData = json_encode($data);
        }
    }
}
