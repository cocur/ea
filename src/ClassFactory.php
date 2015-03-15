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

    /** @var array[] */
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
     * @param string      $name
     * @param string|null $visibility
     * @param bool|null   $static
     * @param mixed|null  $default
     *
     * @return $this
     */
    public function addProperty($name, $visibility = 'public', $static = null, $default = null)
    {
        $static = $static ? true : false;
        $visibility = in_array($visibility, ['public', 'protected', 'private']) ? $visibility : 'public';
        $property = ['name' => $name, 'visibility' => $visibility, 'static' => $static];
        if (func_num_args() === 4) {
            $property['default'] = $default;
        }
        $this->properties[] = $property;

        return $this;
    }

    /**
     * @return array[]
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
            $code .= $this->generateProperty($property);
        }

        return $code;
    }

    /**
     * @param array $property
     *
     * @return string
     */
    protected function generateProperty(array $property)
    {
        $code = "\n    ".$property['visibility'];
        if ($property['static']) {
            $code .= ' static';
        }
        $code .= ' $'.$property['name'];
        if (array_key_exists('default', $property)) {
            $default = $property['default'];
            if ($default === null) {
                $default = 'null';
            } else if ($default !== '[]' && $default != 'array()' && is_string($default)) {
                $default = '\''.$default.'\'';
            }
            $code .= ' = '.$default;
        }

        return $code.';';
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
