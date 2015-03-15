<?php

namespace Cocur\Ea;

use PHPUnit_Framework_TestCase;

/**
 * ClassFactoryTest
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class ClassFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::__construct()
     * @covers Cocur\Ea\ClassFactory::getName()
     */
    public function __constructSetsNameAndGetNameReturnsName()
    {
        $c = new ClassFactory('Foobar');

        $this->assertSame('Foobar', $c->getName());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::__construct()
     * @covers Cocur\Ea\ClassFactory::getNamespace()
     */
    public function __constructSetsNamespaceAndGetNamespaceReturnsNamespace()
    {
        $c = new ClassFactory('Foobar', 'Qoo');

        $this->assertSame('Qoo', $c->getNamespace());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::generate()
     * @covers Cocur\Ea\ClassFactory::generateNamespace()
     */
    public function generateReturnsSourceCodeOfClass()
    {
        $c = new ClassFactory('Foobar');

        $this->assertSame('class Foobar {}', $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::generate()
     * @covers Cocur\Ea\ClassFactory::generateNamespace()
     */
    public function generateReturnsSourceCodeOfClassWithName()
    {
        $c = new ClassFactory('Foobar', 'Qoo');

        $this->assertSame("namespace Qoo;\nclass Foobar {}", $c->generate());
    }
}
