<?php

namespace Cocur\Ea;

/**
 * PropertyFactory
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class PropertyFactory
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $visibility;

    /** @var bool */
    protected $static = false;

    /** @var bool */
    protected $hasDefault = false;

    /** @var mixed|null */
    protected $default = null;

    /**
     * @param string $name
     * @param string $visibility
     *
     * @return PropertyFactory
     */
    public static function create($name, $visibility = 'public')
    {
        return new self($name, $visibility);
    }

    /**
     * @param string $name
     * @param string $visibility
     */
    public function __construct($name, $visibility = 'public')
    {
        $this->name       = $name;
        $this->visibility = in_array($visibility, ['public', 'protected', 'private']) ? $visibility : 'public';
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
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param bool|null $static
     *
     * @return PropertyFactory|bool
     */
    public function isStatic($static = null)
    {
        if ($static === null) {
            return $this->static;
        }

        $this->static = $static ? true : false;

        return $this;
    }

    /**
     * @param mixed $default
     *
     * @return PropertyFactory
     */
    public function setDefault($default)
    {
        $this->hasDefault = true;
        $this->default    = $default;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return bool `true` if the property has a default value; false otherwise
     */
    public function hasDefault()
    {
        return $this->hasDefault;
    }

    /**
     * @return PropertyFactory
     */
    public function removeDefault()
    {
        $this->hasDefault = false;
        $this->default    = null;

        return $this;
    }

    /**
     * @return string
     */
    public function generate()
    {
        return $this->getVisibility().($this->isStatic() ? ' static' : '').' $'.$this->getName()
            .$this->generateDefault().';';
    }

    /**
     * @return string
     */
    protected function generateDefault()
    {
        if (!$this->hasDefault()) {
            return '';
        }

        $code = ' = ';

        if (is_string($this->getDefault())) {
            $code .= '"'.$this->getDefault().'"';
        } else if ($this->getDefault() === true) {
            $code .= 'true';
        } else if ($this->getDefault() === false) {
            $code .= 'false';
        } else if ($this->getDefault() === null) {
            $code .= 'null';
        } else if (is_array($this->getDefault()) && count($this->getDefault()) === 0) {
            $code .= '[]';
        } else {
            $code .= $this->getDefault();
        }

        return $code;
    }
}
