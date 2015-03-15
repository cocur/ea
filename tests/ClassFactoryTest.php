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

        $this->assertSame('foo', $c->getProperties()[0]->getName());
        $this->assertSame('public', $c->getProperties()[0]->getVisibility());
        $this->assertFalse($c->getProperties()[0]->isStatic());
        $this->assertFalse($c->getProperties()[0]->hasDefault());

        $this->assertSame('bar', $c->getProperties()[1]->getName());
        $this->assertSame('protected', $c->getProperties()[1]->getVisibility());

        $this->assertSame('baz', $c->getProperties()[2]->getName());
        $this->assertTrue($c->getProperties()[2]->isStatic());

        $this->assertSame('boo', $c->getProperties()[3]->getName());
        $this->assertTrue($c->getProperties()[3]->hasDefault());
        $this->assertSame(42, $c->getProperties()[3]->getDefault());
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
     */
    public function generateReturnsSourceCodeOfClassWithProperties()
    {
        $expected = <<<EOF
class Foobar {
    public \$foo;
}
EOF;

        $c = new ClassFactory('Foobar');
        $c->addProperty(new PropertyFactory('foo'));

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

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addGetter()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addGetterAddsGetter()
    {
        $expected = <<<EOF
class Foobar {
    private \$foo;
    function getFoo() { return \$this->foo; }
}
EOF;

        $p = new PropertyFactory('foo', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addGetter($p);

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addIsser()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addIsserAddsIsser()
    {
        $expected = <<<EOF
class Foobar {
    private \$foo;
    function isFoo() { return \$this->foo; }
}
EOF;

        $p = new PropertyFactory('foo', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addIsser($p);

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addSetter()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addSetterAddsSetter()
    {
        $expected = <<<EOF
class Foobar {
    private \$foo;
    function setFoo(\$foo) { \$this->foo = \$foo; }
}
EOF;

        $p = new PropertyFactory('foo', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addSetter($p);

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addAdder()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addAdderAddsAdder()
    {
        $expected = <<<EOF
class Foobar {
    private \$foo;
    function addFoo(\$foo) { \$this->foo[] = \$foo; }
}
EOF;

        $p = new PropertyFactory('foo', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addAdder($p);

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addAdder()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addAdderAddsAdderWithKeyValue()
    {
        $expected = <<<EOF
class Foobar {
    private \$foo;
    function addFoo(\$key, \$foo) { \$this->foo[\$key] = \$foo; }
}
EOF;

        $p = new PropertyFactory('foo', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addAdder($p, true);

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addAdder()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addAdderAddsAdderWithSingular()
    {
        $expected = <<<EOF
class Foobar {
    private \$foos;
    function addFoo(\$foo) { \$this->foos[] = \$foo; }
}
EOF;

        $p = new PropertyFactory('foos', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addAdder($p, false, 'foo');

        $this->assertSame($expected, $c->generate());
    }

    /**
     * @test
     * @covers Cocur\Ea\ClassFactory::addHasser()
     * @covers Cocur\Ea\ClassFactory::generate()
     */
    public function addHasserAddsHasser()
    {
        $expected = <<<EOF
class Foobar {
    private \$foo;
    function hasFoo(\$key) { return isset(\$this->foo[\$key]); }
}
EOF;

        $p = new PropertyFactory('foo', 'private');
        $c = new ClassFactory('Foobar');
        $c->addProperty($p);
        $c->addHasser($p);

        $this->assertSame($expected, $c->generate());
    }
}
