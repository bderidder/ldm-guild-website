<?php

namespace LaDanse\ServicesBundle\Service\DTO\CalendarExport;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class CalendarExportFactory
{
    /**
     * @param Entity\CalendarExport $calendarExport
     *
     * @return CalendarExport
     */
    public static function create(Entity\CalendarExport $calendarExport)
    {
        $factory = new CalendarExportFactory();

        return $factory->createCalendarExport($calendarExport);
    }

    protected function createCalendarExport(Entity\CalendarExport $calendarExport)
    {
        return new CalendarExport(
            $calendarExport->getId(),
            $calendarExport->getExportAbsence(),
            $calendarExport->getExportNew(),
            $calendarExport->getSecret(),
            new AccountReference(
                $calendarExport->getAccount()->getId(),
                $calendarExport->getAccount()->getDisplayName())
        );

    }
}