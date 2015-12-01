<?php

namespace LaDanse\SiteBundle\Model\Calendar;


class CalendarMonthModel
{
    /** @var \DateTime $startDate */
    private $startDate;

    /** @var \DateTime $endDate */
    private $endDate;

    /**
     * Create a CalendarMonthModel that contains the given $date in the first or second week.
     *
     * It is guaranteed that the raid week the given $date is in, is fully shown in the calendar.
     *
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->init($date);
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return clone $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return clone $this->endDate;
    }

    /**
     * Shift the calendar one week to the past.
     *
     * @return CalendarMonthModel
     */
    public function shiftOneWeekBack()
    {
        $this->startDate->modify('-7 days');
        $this->endDate->modify('-7 days');

        return $this;
    }

    /**
     * Verify if given $containedDate falls in the month represented by this model.
     *
     * @param \DateTime $containedDate
     *
     * @return true if the date falls in the represented month, false otherwise
     */
    public function containsDate(\DateTime $containedDate)
    {
        return (($this->startDate <= $containedDate) && ($containedDate <= $this->endDate));
    }

    private function init(\DateTime $date)
    {
        $day = date('w', $date->getTimestamp());

        if ($day === 0)
        {
            // it's Sunday but in Europe we start our weeks on Monday
            $day = 7;
        }

        // days are not from Monday (1) to Sunday (7)

        if ($day < 3)
        {
            // it's monday or tuesday, we need to include the previous week in our
            // calendar to show the current raid week completely

            $day = $day + 6;
        }
        else
        {
            $day = $day - 1;
        }

        $this->startDate = clone $date;
        $this->startDate->modify('-' . $day . ' days');

        $this->endDate = clone $this->startDate;
        $this->endDate->modify('+' . 27 . ' days');
    }
}