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
     * @covers Cocur\Ea\ClassFactory::getNamespace()
     */
    public function __constructSetsNameAndGetNameReturnsName()
    {
        $c = new ClassFactory('Foobar', 'Qoo');
        $this->assertSame('Qoo', $c->getNamespace());

        $this->assertSame('Foobar', $c->getName());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addProperty()
     * @covers Cocur\Ea\ClassFactory::getProperties()
     */
    public function addPropertyAddsPropertiesAndGetPropertyReturnsProperties()
    {
        $c = new ClassFactory('Foobar');
        $c->addProperty('foo');
        $c->addProperty('bar', 'protected');
        $c->addProperty('baz', null, true);
        $c->addProperty('boo', null, null, 42);

        $this->assertCount(4, $c->getProperties());
        $this->assertContains(['name' => 'foo', 'visibility' => 'public', 'static' => false], $c->getProperties());
        $this->assertContains(['name' => 'bar', 'visibility' => 'protected', 'static' => false], $c->getProperties());
        $this->assertContains(['name' => 'baz', 'visibility' => 'public', 'static' => true], $c->getProperties());
        $this->assertContains(
            ['name' => 'boo', 'visibility' => 'public', 'static' => false, 'default' => 42],
            $c->getProperties()
        );
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addMethod()
     * @covers Cocur\Ea\ClassFactory::getMethods()
     */
    public function addMethodAddsMethodAndGetMethodsReturnsMethods()
    {
        $m1 = new MethodFactory('foo');
        $m2 = new MethodFactory('bar');

        $c = new ClassFactory('Foobar');
        $c->addMethod($m1);
        $c->addMethod($m2);

        $this->assertCount(2, $c->getMethods());
        $this->assertContains($m1, $c->getMethods());
        $this->assertContains($m2, $c->getMethods());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::generate()
     * @covers Cocur\Ea\ClassFactory::generateNamespace()
     */
    public function generateReturnsSourceCodeOfClass()
    {
        $c = new ClassFactory('Foobar');

        $this->assertSame("class Foobar {\n}", $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::generate()
     * @covers Cocur\Ea\ClassFactory::generateNamespace()
     */
    public function generateReturnsSourceCodeOfClassWithName()
    {
        $c = new ClassFactory('Foobar', 'Qoo');

        $this->assertSame("namespace Qoo;\nclass Foobar {\n}", $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::generate()
     * @covers Cocur\Ea\ClassFactory::generateProperties()
     * @covers Cocur\Ea\ClassFactory::generateProperty()
     */
    public function generateReturnsSourceCodeOfClassWithProperties()
    {
        $expected = <<<EOF
class Foobar {
    public \$foo;
    protected \$bar;
    public static \$baz;
    public \$boo = 42;
    public \$bom = [];
    public \$bot = null;
    public \$box = 'foo';
}
EOF;

        $c = new ClassFactory('Foobar');
        $c->addProperty('foo');
        $c->addProperty('bar', 'protected');
        $c->addProperty('baz', null, true);
        $c->addProperty('boo', null, null, 42);
        $c->addProperty('bom', null, null, '[]');
        $c->addProperty('bot', null, null, null);
        $c->addProperty('box', null, null, 'foo');

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::generate()
     * @covers Cocur\Ea\ClassFactory::generateMethods()
     */
    public function generateReturnsSourceCodeOfClassWithMethods()
    {
        $expected = <<<EOF
class Foobar {
    function foo() { echo "TEST"; }
}
EOF;

        $m = new MethodFactory('foo');
        $m->setBody('echo "TEST";');

        $c = new ClassFactory('Foobar');
        $c->addMethod($m);

        $this->assertSame($expected, $c->generate());
    }
}
