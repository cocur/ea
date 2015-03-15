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
     * @covers Cocur\Ea\MethodFactory::isStatic()
     */
    public function isStaticSetsStaticAndReturnsStatic()
    {
        $m = new MethodFactory('foo');
        $this->assertFalse($m->isStatic());

        $m->isStatic(true);
        $this->assertTrue($m->isStatic());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::addArgument()
     * @covers Cocur\Ea\MethodFactory::getArguments()
     */
    public function addArgumentAddsArgumentAndGetArgumentReturnsArgument()
    {
        $m = new MethodFactory('foo');
        $m->addArgument('bar');
        $m->addArgument('baz', 'array');
        $m->addArgument('qoo', null, 'qoz');
        $m->addArgument('boo', null, null);

        $this->assertCount(4, $m->getArguments());
        $this->assertContains(['name' => 'bar', 'type' => null], $m->getArguments());
        $this->assertContains(['name' => 'baz', 'type' => 'array'], $m->getArguments());
        $this->assertContains(['name' => 'qoo', 'type' => null, 'default' => 'qoz'], $m->getArguments());
        $this->assertContains(['name' => 'boo', 'type' => null, 'default' => null], $m->getArguments());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::setBody()
     * @covers Cocur\Ea\MethodFactory::getBody()
     */
    public function setBodySetsBodyAndReturnBodyReturnsBody()
    {
        $m = new MethodFactory('foo');
        $m->setBody('echo "TEST";');

        $this->assertSame('echo "TEST";', $m->getBody());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::generate()
     * @covers Cocur\Ea\MethodFactory::generateVisibility()
     * @covers Cocur\Ea\MethodFactory::generateStatic()
     * @covers Cocur\Ea\MethodFactory::generateBody()
     */
    public function generateReturnsCodeOfMethod()
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

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::generate()
     * @covers Cocur\Ea\MethodFactory::generateStatic()
     */
    public function generateReturnsCodeOfMethodWithStatic()
    {
        $m = new MethodFactory('foo');
        $m->isStatic(true);

        $this->assertSame('static function foo() {}', $m->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::generate()
     * @covers Cocur\Ea\MethodFactory::generateArguments()
     * @covers Cocur\Ea\MethodFactory::generateArgument()
     */
    public function generateReturnsCodeOfMethodWithArguments()
    {
        $m = new MethodFactory('foo');
        $m->addArgument('bar');
        $m->addArgument('baz', 'array');
        $m->addArgument('qoo', null, 'qoz');
        $m->addArgument('boo', null, null);

        $this->assertSame('function foo($bar, array $baz, $qoo = \'qoz\', $boo = null) {}', $m->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\MethodFactory::generate()
     * @covers Cocur\Ea\MethodFactory::generateBody()
     */
    public function generateReturnsCodeOfMethodWithBody()
    {
        $m = new MethodFactory('foo');
        $m->setBody('echo "TEST";');

        $this->assertSame('function foo() { echo "TEST"; }', $m->generate());
    }
}
