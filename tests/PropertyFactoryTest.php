<?php

namespace Cocur\Ea;

use PHPUnit_Framework_TestCase;

/**
 * PropertyFactoryTest
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class PropertyFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::__construct()
     * @covers Cocur\Ea\PropertyFactory::getName()
     * @covers Cocur\Ea\PropertyFactory::getVisibility()
     */
    public function __constructCreatesPropertyWithName()
    {
        $p = new PropertyFactory('foo');

        $this->assertSame('foo', $p->getName());
        $this->assertSame('public', $p->getVisibility());
    }
    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::__construct()
     * @covers Cocur\Ea\PropertyFactory::getVisibility()
     */
    public function __constructCreatesPublicPropertyIfVisibilityIsInvalid()
    {
        $p = new PropertyFactory('foo', null);

        $this->assertSame('public', $p->getVisibility());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::__construct()
     * @covers Cocur\Ea\PropertyFactory::getVisibility()
     */
    public function __constructCreatesPropertyWithVisibility()
    {
        $p = new PropertyFactory('foo', 'protected');

        $this->assertSame('foo', $p->getName());
        $this->assertSame('protected', $p->getVisibility());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::isStatic()
     */
    public function isStaticSetsAndGetsStatic()
    {
        $p = new PropertyFactory('foo');
        $this->assertFalse($p->isStatic());

        $p->isStatic(true);
        $this->assertTrue($p->isStatic());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::setDefault()
     * @covers Cocur\Ea\PropertyFactory::getDefault()
     * @covers Cocur\Ea\PropertyFactory::hasDefault()
     * @covers Cocur\Ea\PropertyFactory::removeDefault()
     */
    public function setDefaultSetsDefaultAndGetDefaultReturnsDefault()
    {
        $p = new PropertyFactory('foo');
        $this->assertFalse($p->hasDefault());

        $p->setDefault('baz');
        $this->assertTrue($p->hasDefault());
        $this->assertSame('baz', $p->getDefault());

        $p->removeDefault();
        $this->assertFalse($p->hasDefault());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     * @covers Cocur\Ea\PropertyFactory::generateDefault()
     */
    public function generateGeneratesCodeForPublicProperty()
    {
        $p = new PropertyFactory('foo');

        $this->assertSame('public $foo;', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     */
    public function generateGeneratesCodeForProtectedProperty()
    {
        $p = new PropertyFactory('foo', 'protected');

        $this->assertSame('protected $foo;', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     */
    public function generateGeneratesCodeForStaticProperty()
    {
        $p = new PropertyFactory('foo');
        $p->isStatic(true);

        $this->assertSame('public static $foo;', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     * @covers Cocur\Ea\PropertyFactory::generateDefault()
     */
    public function generateGeneratesCodeForPropertyWithDefaultStringValue()
    {
        $p = new PropertyFactory('foo');
        $p->setDefault('foo');

        $this->assertSame('public $foo = "foo";', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     * @covers Cocur\Ea\PropertyFactory::generateDefault()
     */
    public function generateGeneratesCodeForPropertyWithDefaultIntValue()
    {
        $p = new PropertyFactory('foo');
        $p->setDefault(42);

        $this->assertSame('public $foo = 42;', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     * @covers Cocur\Ea\PropertyFactory::generateDefault()
     */
    public function generateGeneratesCodeForPropertyWithDefaultBoolValue()
    {
        $p = new PropertyFactory('foo');

        $p->setDefault(true);
        $this->assertSame('public $foo = true;', $p->generate());

        $p->setDefault(false);
        $this->assertSame('public $foo = false;', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     * @covers Cocur\Ea\PropertyFactory::generateDefault()
     */
    public function generateGeneratesCodeForPropertyWithDefaultNullValue()
    {
        $p = new PropertyFactory('foo');

        $p->setDefault(null);
        $this->assertSame('public $foo = null;', $p->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\PropertyFactory::generate()
     * @covers Cocur\Ea\PropertyFactory::generateDefault()
     */
    public function generateGeneratesCodeForPropertyWithDefaultEmptyArrayValue()
    {
        $p = new PropertyFactory('foo');

        $p->setDefault([]);
        $this->assertSame('public $foo = [];', $p->generate());
    }
}
