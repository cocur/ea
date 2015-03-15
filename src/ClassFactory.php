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
     * @return string Source code of class
     */
    public function generate()
    {
        $template = '%namespace%class %name% {}';

        return str_replace(['%name%', '%namespace%'], [$this->getName(), $this->generateNamespace()], $template);
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
}
