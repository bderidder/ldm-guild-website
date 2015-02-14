<?php

namespace LaDanse\SiteBundle\Form\Model;

class CalExportFormModel
{
    /** @var $newEvents boolean */
    private $exportSignUp;

    /** @var $changeSignedEvent boolean */
    private $exportAbsence;

    /** @var $signUpChange boolean */
    private $exportNew;

    /**
     * @return boolean
     */
    public function isExportSignUp()
    {
        return $this->exportSignUp;
    }

    /**
     * @param boolean $exportSignUp
     */
    public function setExportSignUp($exportSignUp)
    {
        $this->exportSignUp = $exportSignUp;
    }

    /**
     * @return boolean
     */
    public function isExportAbsence()
    {
        return $this->exportAbsence;
    }

    /**
     * @param boolean $exportAbsence
     */
    public function setExportAbsence($exportAbsence)
    {
        $this->exportAbsence = $exportAbsence;
    }

    /**
     * @return boolean
     */
    public function isExportNew()
    {
        return $this->exportNew;
    }

    /**
     * @param boolean $exportNew
     */
    public function setExportNew($exportNew)
    {
        $this->exportNew = $exportNew;
    }
}