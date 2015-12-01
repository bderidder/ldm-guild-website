<?php

namespace LaDanse\SiteBundle\Model;

use LaDanse\SiteBundle\Controller\Calendar\CalendarController;

class RaidWeekModel
{
    /** @var \DateTime $firstData */
    protected $firstDate;

    /** @var \DateTime $lastDate */
    protected $lastDate;

    /**
     * Create a RaidWeekModel that represents the raid week containing the given $date.
     *
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->init($date);
    }

    public function inRaidWeek(\DateTime $date)
    {
        return (
            ($date->format(CalendarController::COMPARE_DATE_FORMAT)
                <= $this->lastDate->format(CalendarController::COMPARE_DATE_FORMAT))
        &&
            ($date->format(CalendarController::COMPARE_DATE_FORMAT)
                >= $this->firstDate->format(CalendarController::COMPARE_DATE_FORMAT))
        );
    }

    private function init(\DateTime $date)
    {
        $dayInWeek = date('w', $date->getTimestamp());

        // 0 = Sunday, 1 = Monday ... 6 = Saturday

        $deltaToStart = 0;
        $deltaToEnd = 0;

        // this can be done "smarter" using clever mathematics but harder to validate
        switch ($dayInWeek)
        {
            case 0: // Sunday
                $deltaToStart = 4;
                $deltaToEnd   = 2;
                break;
            case 1: // Monday
                $deltaToStart = 5;
                $deltaToEnd   = 1;
                break;
            case 2: // Tuesday (= end of raid week)
                $deltaToStart = 6;
                $deltaToEnd   = 0;
                break;
            case 3: // Wednesday (= start of raid week)
                $deltaToStart = 0;
                $deltaToEnd   = 6;
                break;
            case 4: // Thursday
                $deltaToStart = 1;
                $deltaToEnd   = 5;
                break;
            case 5: // Friday
                $deltaToStart = 2;
                $deltaToEnd   = 4;
                break;
            case 6: // Saturday
                $deltaToStart = 3;
                $deltaToEnd   = 3;
                break;
        }

        $tempFirst = strtotime('-' . $deltaToStart . ' days', $date->getTimestamp());
        $this->firstDate = \DateTime::createFromFormat("d/m/Y", date('d/m/Y', $tempFirst));

        $tempLast = strtotime('+' . $deltaToEnd . ' days', $date->getTimestamp());
        $this->lastDate = \DateTime::createFromFormat("d/m/Y", date('d/m/Y', $tempLast));

    }
}
