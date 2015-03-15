<?php

require_once __DIR__.'/../vendor/autoload.php';

use Cocur\Ea\ClassFactory;
use Cocur\Ea\Ea;
use Cocur\Ea\PropertyFactory;

Ea::create()
    ->addClass(ClassFactory::create('Foo', 'Foobar')->addProperty(PropertyFactory::create('foo')))
    ->execute();

$foo = new Foobar\Foo();
$foo->foo = 'foo';

echo "foo? foo=".$foo->foo."\n";
