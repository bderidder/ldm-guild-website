<?php

namespace LaDanse\SiteBundle\Tests\Model\Calendar;

use LaDanse\SiteBundle\Model\Calendar\CalendarMonthModel;

class CalendarMonthModelTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorMonday()
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel(new \DateTime('2015-12-07'));

        $this->assertEquals($calendarMonthModel->getStartDate(),  new \DateTime('2015-11-30'));
        $this->assertEquals($calendarMonthModel->getEndDate(), new \DateTime('2015-12-27'));
    }

    public function testConstructorTuesday()
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel(new \DateTime('2015-12-08'));

        $this->assertEquals($calendarMonthModel->getStartDate(),  new \DateTime('2015-11-30'));
        $this->assertEquals($calendarMonthModel->getEndDate(), new \DateTime('2015-12-27'));
    }

    public function testConstructorWednesday()
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel(new \DateTime('2015-12-09'));

        $this->assertEquals($calendarMonthModel->getStartDate(),  new \DateTime('2015-12-07'));
        $this->assertEquals($calendarMonthModel->getEndDate(), new \DateTime('2016-01-03'));
    }

    public function testConstructorSunday()
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel(new \DateTime('2015-12-06'));

        $this->assertEquals($calendarMonthModel->getStartDate(),  new \DateTime('2015-11-30'));
        $this->assertEquals($calendarMonthModel->getEndDate(), new \DateTime('2015-12-27'));
    }

    public function testShiftOneWeekBack()
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel(new \DateTime('2015-12-13'));

        $calendarMonthModel->shiftOneWeekBack();

        $this->assertEquals($calendarMonthModel->getStartDate(),  new \DateTime('2015-11-30'));
        $this->assertEquals($calendarMonthModel->getEndDate(), new \DateTime('2015-12-27'));
    }

    public function testContainsDate()
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel(new \DateTime('2015-12-06'));

        $this->assertTrue($calendarMonthModel->containsDate(new \DateTime('2015-12-06')));
        $this->assertTrue($calendarMonthModel->containsDate(new \DateTime('2015-11-30')));
        $this->assertTrue($calendarMonthModel->containsDate(new \DateTime('2015-12-27')));
        $this->assertTrue($calendarMonthModel->containsDate(new \DateTime('2015-12-15')));

        $this->assertFalse($calendarMonthModel->containsDate(new \DateTime('2015-11-29')));
        $this->assertFalse($calendarMonthModel->containsDate(new \DateTime('2015-12-28')));
    }
}
