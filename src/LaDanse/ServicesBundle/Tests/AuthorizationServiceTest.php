<?php

namespace LaDanse\ServicesBundle\Tests;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\NullResourceReference;
use LaDanse\ServicesBundle\Service\Authorization\ResourceById;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Authorization\CannotEvaluateException;
use LaDanse\ServicesBundle\Service\Authorization\UnresolvableResourceException;
use LaDanse\ServicesBundle\Service\Authorization\NoMatchingPolicyFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group UnitTest
 */
class AuthorizationServiceTest extends KernelTestCase
{
    /** @var AuthorizationService */
    private $authzService;

    /** @var Account */
    private $mockAccount;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->authzService = static::$kernel->getContainer()
            ->get(AuthorizationService::SERVICE_NAME);

        $this->mockAccount = \Phake::mock(Account::class);

        \Phake::when($this->mockAccount)->getId()->thenReturn(1);
    }

    public function testPoliciesFound()
    {
        $evalResult = $this->authzService->evaluate(
            new SubjectReference($this->mockAccount),
            ActivityType::EVENT_EDIT,
            new ResourceById(Event::class, 1)
        );

        $this->assertTrue($evalResult);
    }

    public function testNoResource()
    {
        try
        {
            $this->authzService->evaluate(
                new SubjectReference($this->mockAccount),
                ActivityType::EVENT_EDIT,
                new NullResourceReference()
            );

            $this->fail("Expected exception CannotEvaluateException but was not thrown");
        }
        catch(\Exception $e)
        {
            $this->assertEquals(CannotEvaluateException::class, get_class($e));
            $this->assertNotNull($e->getPrevious());
            $this->assertEquals(UnresolvableResourceException::class, get_class($e->getPrevious()));
        }
    }

    public function testNoPoliciesFound()
    {
        try
        {
            $this->authzService->evaluate(
                new SubjectReference($this->mockAccount),
                ActivityType::ABOUT_VIEW,
                new ResourceById(Event::class, 1)
            );

            $this->fail("Expected exception CannotEvaluateException but was not thrown");
        }
        catch(\Exception $e)
        {
            $this->assertEquals(CannotEvaluateException::class, get_class($e));
            $this->assertNotNull($e->getPrevious());
            $this->assertEquals(NoMatchingPolicyFoundException::class, get_class($e->getPrevious()));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
}
