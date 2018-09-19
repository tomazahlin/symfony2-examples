<?php

namespace ExampleProject\Bundle\UserBundle\Tests\Entity;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ExampleProject\Bundle\UserBundle\Entity\Ignore;
use ExampleProject\Bundle\UserBundle\Entity\User;

class IgnoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that the Ignore object constructor accepts valid data and tests the references.
     *
     * @group user
     * @group ignore
     */
    public function testUserConstruction()
    {
        $ignore = $this->createIgnore();

        $this->assertInstanceOf('ExampleProject\Bundle\UserBundle\Entity\User', $ignore->getOwner());
        $this->assertInstanceOf('ExampleProject\Bundle\UserBundle\Entity\User', $ignore->getTarget());
    }

    /**
     * @return Ignore
     */
    private function createIgnore()
    {
        return new Ignore($this->createUser(), $this->createUser(), 0);
    }

    /**
     * @return User
     */
    private function createUser()
    {
        $reflection = new ReflectionClass('ExampleProject\Bundle\UserBundle\Entity\User');

        return $reflection->newInstanceWithoutConstructor();
    }

    /**
     * Tests that the Ignore object can have levels changed.
     *
     * @group user
     * @group ignore
     */
    public function testLevelChange()
    {
        $ignore = $this->createIgnore();

        $this->assertEquals(0, $ignore->getLevel());
        $this->assertFalse($ignore->hasCompleteLevel());
        $this->assertFalse($ignore->hasVisitorLevel());
        $this->assertFalse($ignore->hasSearchLevel());

        $ignore->addLevel(Ignore::LEVEL_VISITOR);

        $this->assertFalse($ignore->isDeletable());

        $this->assertEquals(2, $ignore->getLevel());
        $this->assertFalse($ignore->hasCompleteLevel());
        $this->assertTrue($ignore->hasVisitorLevel());
        $this->assertFalse($ignore->hasSearchLevel());

        $ignore->addLevel(Ignore::LEVEL_COMPLETE);

        $this->assertEquals(3, $ignore->getLevel());
        $this->assertTrue($ignore->hasCompleteLevel());
        $this->assertTrue($ignore->hasVisitorLevel());
        $this->assertTrue($ignore->hasSearchLevel());

        $ignore->removeLevel(Ignore::LEVEL_VISITOR);

        $this->assertEquals(1, $ignore->getLevel());
        $this->assertTrue($ignore->hasCompleteLevel());
        $this->assertTrue($ignore->hasVisitorLevel());
        $this->assertTrue($ignore->hasSearchLevel());

        $ignore->removeLevel(Ignore::LEVEL_COMPLETE);

        $this->assertEquals(0, $ignore->getLevel());
        $this->assertFalse($ignore->hasCompleteLevel());
        $this->assertFalse($ignore->hasVisitorLevel());
        $this->assertFalse($ignore->hasSearchLevel());

        $this->assertTrue($ignore->isDeletable());
    }

    /**
     * Tests that the Ignore object can have levels decomposed.
     *
     * @group user
     * @group ignore
     */
    public function testLevelsDecomposition()
    {
        $ignore = $this->createIgnore();

        $ignore->addLevel(Ignore::LEVEL_COMPLETE);
        $ignore->addLevel(Ignore::LEVEL_SEARCH);

        $this->assertContains(1, $ignore->getLevels());
        $this->assertContains(4, $ignore->getLevels());

        $this->assertNotContains(2, $ignore->getLevels());
    }
}
