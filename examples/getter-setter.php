<?php

require_once __DIR__.'/../vendor/autoload.php';

use Cocur\Ea\ClassFactory;
use Cocur\Ea\Ea;
use Cocur\Ea\PropertyFactory;

$name = PropertyFactory::create('name', 'private');

Ea::create()
    ->addClass(
        ClassFactory::create('Foo', 'Foobar')
            ->addProperty($name)
            ->addGetter($name)
            ->addSetter($name)
    )
    ->execute();

$foo = new Foobar\Foo();
$foo->setName('Florian');

echo "name: ".$foo->getName()."\n";
