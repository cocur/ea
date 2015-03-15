Ea
==

> Code generation for testing. Because mocks sometimes just don't cut it.

Developed by [Florian Eckerstorfer](https://florian.ec) in Vienna, Europe.


Features
--------

- Useful if the code under test contains calls to functions like `method_exists()`
- Generate classes with methods and properties
- Generate setters, getters, hassers, issers, and adders based on properties


Installation
------------

You can install Ea using [Composer](https://getcomposer.org):

```shell
$ composer require cocur/ea:dev-master
```


Usage
-----

*Ea was developed to generate code for testing purposes. For example, if your code under test contains a call to
a function like `method_exists()` typical mocks don't work.*

You can generate a class using `ClassFactory`. The first argument is the class name and the second argument is the
namespace (which is optional):

```php
$class = new ClassFactory('Foo', 'Foobar');
echo $class->generate();
```

will output

```php
namespace Foobar;
class Foo {
}
```

You can create properties with `PropertyFactory` and use the `addProperty()` method to add them to a class. Properties
have a name, a visibility (`public`, `protected` or `private`), can be static and can have a default value.

```php
$property = new PropertyFactory('foo, 'public');
$property->isStatic(true);
$property->setDefault('bar');
$property->generate(); // "public static $foo = 'bar';"
```

If you call the `setDefault()` method a default value will be set, even if you use `null` or `false` as argument. The
value given to `setDefault()` will be used as default. If you don't call `setDefault()` no default value will be set.
You can check if a property has a default value using `hasDefault()` and remove it using `removeDefault()`.
 
Alternatively the `addProperty()` method of `ClassFactory` can create the `PropertyFactory` object for you, if you
call it with a string as first parameter instead of an instance of `PropertyFactory`. The second argument is the
visibility, the third static and the fourth the default value. If you call `addProperty()` with four arguments a
default value will be set, if not no default value will be set.

```php
$class->addProperty('foo', 'protected', false, null);
$class->addProperty('bar', 'public', true);
echo $class->generate();
```

will result in

```php
// ...
    protected $foo = null;
    public static $bar;
// ...
```

Methods are created using `MethodFactory` and added to the class by calling `addMethod()`.

```php
$method = new MethodFactory('foo', 'protected');
$method->isStatic(false);
$method->addArgument('foo', null, 21);
$method->setBody('return 21+$foo');
$method->generate(); // "protected function foo($foo = 21) { return 21+$foo; }"
```

The constructor takes the name and the visibility (`public`, `protected`, `private`) as arguments and `isStatic()` sets
whether the method should be static or not. The code passed to `setBody()` will be returned as-is as body of the
method. The `addArgument()` method takes the name of the argument as first parameter, the type (like `array` or
`stdClass`; none by default) as second argument and the default value as third argument. If three arguments are provided
the default value will always be set; if less arguments are provided no default value will be set.

For some common property access methods, such as setters, getters, hassers, adders and issers, the `ClassFactory`
provides helper methods to quickly create these based on a given `PropertyFactory`.

```php
$class->addGetter($property);
$class->addSetter($property);
$class->addIsser($property);
$class->addHasser($property);
```

The `addAdder()` method is a little bit different, as it provides more options. The second argument is a `bool` to
indicate whether the value should be added by a key. The third argument is the singular name of the property. Example:

```php
$bars = PropertyFactory('bars');
$class->addAdder($bars, true, 'bar');
$class->generate(); // -> "public function addBar($key, $bar) { $this->bars[$key] = $bar; }"
```

Instead of generating the code you can also dynamically create the classes using `execute()` of the `Ea`.
**Attention. Internally Ea uses `eval()` to create the classes.**
 
```php
Ea::create()->addClass($class)->execute();
```


License
-------

The MIT license applies to Ea. For the full copyright and license information, please view the 
[LICENSE](https://github.com/cocur/ea/blob/master/LICENSE) file distributed with this source code.
