<?php

namespace LaDanse\SiteBundle\Tests\Unit\Model\Calendar;

use LaDanse\SiteBundle\Model\Calendar\RaidWeekModel;

/**
 * @group UnitTest
 */
class RaidWeekModelTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorMonday()
    {
        /** @var RaidWeekModel $raidWeekModel */
        $raidWeekModel = new RaidWeekModel(new \DateTime('2015-12-07'));

        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-07')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-02')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-08')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-01')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-09')));
    }

    public function testConstructorTuesday()
    {
        /** @var RaidWeekModel $raidWeekModel */
        $raidWeekModel = new RaidWeekModel(new \DateTime('2015-12-08'));

        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-07')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-02')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-08')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-01')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-09')));
    }

    public function testConstructorWednesday()
    {
        /** @var RaidWeekModel $raidWeekModel */
        $raidWeekModel = new RaidWeekModel(new \DateTime('2015-12-02'));

        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-07')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-02')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-08')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-01')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-09')));
    }

    public function testConstructorSunday()
    {
        /** @var RaidWeekModel $raidWeekModel */
        $raidWeekModel = new RaidWeekModel(new \DateTime('2015-12-06'));

        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-07')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-02')));
        $this->assertTrue($raidWeekModel->inRaidWeek(new \DateTime('2015-12-08')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-01')));
        $this->assertFalse($raidWeekModel->inRaidWeek(new \DateTime('2015-12-09')));
    }
}
