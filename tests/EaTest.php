<?php


namespace Cocur\Ea;

use PHPUnit_Framework_TestCase;

/**
 * EaTest
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class EaTest extends PHPUnit_Framework_TestCase
{
    /** @var Ea */
    private $ea;

    public function setUp()
    {
        $this->ea = new Ea();
    }

    /**
     * @test
     * @covers Cocur\Ea\Ea::create()
     */
    public function createCreatesInstance()
    {
        $this->assertInstanceOf('Cocur\Ea\Ea', Ea::create());
    }

    /**
     * @test
     * @covers Cocur\Ea\Ea::addClass()
     * @covers Cocur\Ea\Ea::getClasses()
     */
    public function addClassAddsClassAndGetClassesReturnsClasses()
    {
        $c1 = new ClassFactory('Foo');
        $c2 = new ClassFactory('Bar');

        $this->ea->addClass($c1);
        $this->ea->addClass($c2);

        $this->assertCount(2, $this->ea->getClasses());
        $this->assertContains($c1, $this->ea->getClasses());
        $this->assertContains($c2, $this->ea->getClasses());
    }

    /**
     * @test
     * @covers Cocur\Ea\Ea::generate()
     */
    public function generateGeneratesClasses()
    {
        $expected = <<<EOF
class Foo {
}
class Bar {
}
EOF;

        $this->ea->addClass(new ClassFactory('Foo'));
        $this->ea->addClass(new ClassFactory('Bar'));

        $this->assertSame($expected, $this->ea->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\Ea::execute()
     */
    public function executeExecutesCode()
    {
        $this->ea->addClass(new ClassFactory('Foo123'))
                 ->addClass(new ClassFactory('Bar123'))
                 ->execute();

        $this->assertTrue(class_exists('Foo123'));
        $this->assertTrue(class_exists('Bar123'));
    }
}
