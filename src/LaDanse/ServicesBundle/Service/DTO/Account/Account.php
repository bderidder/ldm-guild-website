<?php

namespace LaDanse\ServicesBundle\Service\DTO\Account;

use JMS\Serializer\Annotation\SerializedName;

class Account
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * @SerializedName("username")
     *
     * @var string
     */
    protected $username;

    /**
     * @SerializedName("displayName")
     *
     * @var string
     */
    protected $displayName;

    /**
     * @SerializedName("email")
     *
     * @var string
     */
    protected $email;

    /**
     * @SerializedName("enabled")
     *
     * @var boolean
     */
    protected $enabled;

    /**
     * @SerializedName("lastLogin")
     *
     * @var \DateTime
     */
    protected $lastLogin;

    public function __construct($id,
                                $username,
                                $displayName,
                                $email,
                                $enabled,
                                $lastLogin)
    {
        $this->id = $id;
        $this->username = $username;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->enabled = $enabled;
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
}