<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\CalendarExport;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO as DTO;

class CalendarExport
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * @SerializedName("exportNew")
     *
     * @var boolean
     */
    protected $exportNew;

    /**
     * @SerializedName("exportAbsence")
     *
     * @var boolean
     */
    protected $exportAbsence;

    /**
     * @SerializedName("secret")
     *
     * @var string
     */
    protected $secret;

    /**
     * @SerializedName("accountRef")
     *
     * @var DTO\Reference\AccountReference
     */
    protected $accountRef;

    public function __construct($id,
                                $exportNew,
                                $exportAbsence,
                                $secret,
                                $accountRef)
    {
        $this->id = $id;
        $this->exportNew = $exportNew;
        $this->exportAbsence = $exportAbsence;
        $this->secret = $secret;
        $this->accountRef = $accountRef;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isExportNew()
    {
        return $this->exportNew;
    }

    /**
     * @return boolean
     */
    public function isExportAbsence()
    {
        return $this->exportAbsence;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return DTO\Reference\AccountReference
     */
    public function getAccountRef()
    {
        return $this->accountRef;
    }
}
