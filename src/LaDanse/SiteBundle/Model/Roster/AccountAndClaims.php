<?php

namespace LaDanse\SiteBundle\Model\Roster;

class AccountAndClaims
{
    /** @var integer */
    private $id;
    /** @var string */
    private $displayName;
    /** @var array */
    private $claims;

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
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return array
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * @param string $claim
     */
    public function addClaim($claim)
    {
        $this->claims[] = $claim;

        usort(
            $this->claims,
            function ($a, $b)
            {
                return strcmp($a, $b);
            }
        );
    }
}
