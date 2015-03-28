<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ActivityQueueItem")
 * @ORM\HasLifecycleCallbacks
 */
class ActivityQueueItem
{
    const REPOSITORY = 'LaDanseDomainBundle:ActivityQueueItem';

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
    protected $activityType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $activityOn;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="activityBy", referencedColumnName="id", nullable=true)
     */
    protected $activityBy;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $rawData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $processedOn;

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
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     * @param string $activityType
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }

    /**
     * @return \DateTime
     */
    public function getActivityOn()
    {
        return $this->activityOn;
    }

    /**
     * @param \DateTime $activityOn
     */
    public function setActivityOn($activityOn)
    {
        $this->activityOn = $activityOn;
    }

    /**
     * @return Account
     */
    public function getActivityBy()
    {
        return $this->activityBy;
    }

    /**
     * @param Account $activityBy
     */
    public function setActivityBy($activityBy)
    {
        $this->activityBy = $activityBy;
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
     * @return \DateTime
     */
    public function getProcessedOn()
    {
        return $this->processedOn;
    }

    /**
     * @param \DateTime $processedOn
     */
    public function setProcessedOn($processedOn)
    {
        $this->processedOn = $processedOn;
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
