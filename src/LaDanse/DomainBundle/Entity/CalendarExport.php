<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarExport")
 * @ORM\HasLifecycleCallbacks
 */
class CalendarExport
{
    const REPOSITORY = 'LaDanseDomainBundle:CalendarExport';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $exportNew;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $exportAbsence;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $secret;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    protected $account;

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
     * @return bool
     */
    public function getExportNew()
    {
        return $this->exportNew;
    }

    /**
     * @param bool $exportNew
     */
    public function setExportNew($exportNew)
    {
        $this->exportNew = $exportNew;
    }

    /**
     * @return bool
     */
    public function getExportAbsence()
    {
        return $this->exportAbsence;
    }

    /**
     * @param bool $exportAbsence
     */
    public function setExportAbsence($exportAbsence)
    {
        $this->exportAbsence = $exportAbsence;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return \LaDanse\DomainBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param \LaDanse\DomainBundle\Entity\Account $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }
}
