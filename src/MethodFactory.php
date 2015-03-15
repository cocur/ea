<?php

namespace Cocur\Ea;

/**
 * MethodFactory
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class MethodFactory
{
    /** @var string */
    private $name;

    /** @var string */
    private $visibility;

    /** @var bool */
    private $static = false;

    /** @var array[] */
    private $arguments = [];

    /** @var string */
    private $body;

    /**
     * @param string      $name
     * @param string|null $visibility
     */
    public function __construct($name, $visibility = null)
    {
        $this->name = $name;
        if ($visibility && in_array($visibility, ['public', 'protected', 'private'])) {
            $this->visibility = $visibility;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param null|bool $static
     *
     * @return $this|bool
     */
    public function isStatic($static = null)
    {
        if ($static !== null) {
            $this->static = $static;

            return $this;
        }

        return $this->static;
    }

    /**
     * @param string      $name
     * @param string|null $type
     * @param mixed|null  $default
     *
     * @return MethodFactory
     */
    public function addArgument($name, $type = null, $default = null)
    {
        $argument = ['name' => $name, 'type' => $type];
        if (func_num_args() === 3) {
            $argument['default'] = $default;
        }
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * @return array[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $template = '%visibility%%static%function %name%(%arguments%) {%body%}';

        return str_replace(
            ['%visibility%', '%static%', '%name%', '%arguments%', '%body%'],
            [
                $this->generateVisibility(),
                $this->generateStatic(),
                $this->getName(),
                $this->generateArguments(),
                $this->generateBody()
            ],
            $template
        );
    }

    /**
     * @return string
     */
    protected function generateVisibility()
    {
        if ($this->getVisibility()) {
            return $this->getVisibility().' ';
        }
        return '';
    }

    /**
     * @return string
     */
    protected function generateStatic()
    {
        if ($this->static === true) {
            return 'static ';
        }
        return '';
    }

    /**
     * @return string
     */
    protected function generateArguments()
    {
        $parts = [];
        foreach ($this->getArguments() as $argument) {
            $parts[] = $this->generateArgument($argument);
        }

        return implode(', ', $parts);
    }

    /**
     * @param array $argument
     *
     * @return string
     */
    protected function generateArgument($argument)
    {
        $part = '';
        if ($argument['type'] !== null) {
            $part .= $argument['type'].' ';
        }
        $part .= '$'.$argument['name'];
        if (array_key_exists('default', $argument)) {
            $default = $argument['default'];
            if ($default === null) {
                $default = 'null';
            } else if ($default !== '[]' && $default != 'array()' && is_string($default)) {
                $default = '\''.$default.'\'';
            }
            $part .= ' = '.$default;
        }

        return $part;
    }

    /**
     * @return string
     */
    protected function generateBody()
    {
        if ($this->getBody()) {
            return ' '.$this->getBody().' ';
        }
        return '';
    }
}
