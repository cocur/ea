<?php

namespace Cocur\Ea;

use PHPUnit_Framework_TestCase;

/**
 * MethodFactoryTest
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class MethodFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::__construct()
     * @covers Cocur\Ea\MethodFactory::getName()
     * @covers Cocur\Ea\MethodFactory::getVisibility()
     */
    public function __constructSetsNameAndVisibility()
    {
        $m = new MethodFactory('foo', 'protected');

        $this->assertSame('foo', $m->getName());
        $this->assertSame('protected', $m->getVisibility());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::generate()
     * @covers Cocur\Ea\MethodFactory::generateVisibility()
     */
    public function generateReturnsCodeOfMethodWith()
    {
        $m = new MethodFactory('foo');

        $this->assertSame('function foo() {}', $m->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::generate()
     * @covers Cocur\Ea\MethodFactory::generateVisibility()
     */
    public function generateReturnsCodeOfMethodWithVisibility()
    {
        $m = new MethodFactory('foo', 'protected');

        $this->assertSame('protected function foo() {}', $m->generate());
    }
}
