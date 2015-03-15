<?php

namespace Cocur\Ea;

/**
 * ClassFactory
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class ClassFactory
{
    /** @var string */
    protected $name;

    /** @var string */
    private $namespace;

    /** @var PropertyFactory[] */
    protected $properties = [];

    /** @var MethodFactory[] */
    protected $methods = [];

    /**
     * @param string $name
     * @param string $namespace
     */
    public function __construct($name, $namespace = null)
    {
        $this->name      = $name;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param PropertyFactory|string $property
     * @param string|null            $visibility
     * @param bool|null              $static
     * @param mixed|null             $default
     *
     * @return ClassFactory
     */
    public function addProperty($property, $visibility = 'public', $static = null, $default = null)
    {
        if (!$property instanceof PropertyFactory) {
            $property = new PropertyFactory($property, $visibility);
            $property->isStatic($static);
            if (func_num_args() === 4) {
                $property->setDefault($default);
            }
        }
        $this->properties[] = $property;

        return $this;
    }

    /**
     * @return PropertyFactory[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param MethodFactory $method
     *
     * @return ClassFactory
     */
    public function addMethod(MethodFactory $method)
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * @return MethodFactory[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param PropertyFactory $property
     *
     * @return ClassFactory
     */
    public function addGetter(PropertyFactory $property)
    {
        $getter = new MethodFactory('get'.ucfirst($property->getName()));
        $getter->setBody('return $this->'.$property->getName().';');

        $this->addMethod($getter);

        return $this;
    }

    /**
     * @param PropertyFactory $property
     *
     * @return ClassFactory
     */
    public function addIsser(PropertyFactory $property)
    {
        $isser = new MethodFactory('is'.ucfirst($property->getName()));
        $isser->setBody('return $this->'.$property->getName().';');

        $this->addMethod($isser);

        return $this;
    }

    /**
     * @param PropertyFactory $property
     *
     * @return ClassFactory
     */
    public function addSetter(PropertyFactory $property)
    {
        $setter = new MethodFactory('set'.ucfirst($property->getName()));
        $setter->addArgument($property->getName());
        $setter->setBody('$this->'.$property->getName().' = $'.$property->getName().';');

        $this->addMethod($setter);

        return $this;
    }

    /**
     * @param PropertyFactory $property
     * @param bool            $keyValue
     * @param string|null     $singular Singular version of the property name
     *
     * @return ClassFactory
     */
    public function addAdder(PropertyFactory $property, $keyValue = false, $singular = null)
    {
        $singular = $singular === null ? $property->getName() : $singular;
        $adder = new MethodFactory('add'.ucfirst($singular));
        if ($keyValue) {
            $adder->addArgument('key');
        }
        $adder->addArgument($singular);
        $adder->setBody('$this->'.$property->getName().'['.($keyValue?'$key':'').'] = $'.$singular.';');

        $this->addMethod($adder);

        return $this;
    }

    /**
     * @param PropertyFactory $property
     *
     * @return ClassFactory
     */
    public function addHasser(PropertyFactory $property)
    {
        $hasser = new MethodFactory('has'.ucfirst($property->getName()));
        $hasser->addArgument('key');
        $hasser->setBody('return isset($this->'.$property->getName().'[$key]);');

        $this->addMethod($hasser);

        return $this;
    }

    /**
     * @return string Source code of class
     */
    public function generate()
    {
        $template = "%namespace%class %name% {%properties%%methods%\n}";

        return str_replace(
            ['%name%', '%namespace%', '%properties%', '%methods%'],
            [$this->getName(), $this->generateNamespace(), $this->generateProperties(), $this->generateMethods()],
            $template
        );
    }

    /**
     * @return string
     */
    protected function generateNamespace()
    {
        $template = "namespace %namespace%;\n";

        if ($this->namespace) {
            return str_replace('%namespace%', $this->getNamespace(), $template);
        }
        return '';
    }

    /**
     * @return string
     */
    protected function generateProperties()
    {
        $code = '';
        foreach ($this->getProperties() as $property) {
            $code .= "\n    ".$property->generate();
        }

        return $code;
    }

    /**
     * @return string
     */
    protected function generateMethods()
    {
        $code = '';
        foreach ($this->getMethods() as $method) {
            $lines = explode("\n", $method->generate());
            for ($i = 0; $i < count($lines); $i++) {
                $lines[$i] = '    '.$lines[$i];
            }
            $code .= "\n".implode("\n", $lines);
        }

        return $code;
    }
}
