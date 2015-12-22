<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class SignUp
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * @SerializedName("accountRef")
     *
     * @var AccountReference
     */
    protected $account;

    /**
     * @SerializedName("type")
     *
     * @var string
     */
    protected $type;

    /**
     * @SerializedName("roles")
     *
     * @var array
     */
    protected $roles;

    /**
     * SignUp constructor.
     *
     * @param int $id
     * @param AccountReference $account
     * @param string $type
     * @param array $roles
     */
    public function __construct($id,
                                AccountReference $account,
                                $type,
                                array $roles = null)
    {
        $this->id = $id;
        $this->account = $account;
        $this->type = $type;
        $this->roles = $roles;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AccountReference
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
