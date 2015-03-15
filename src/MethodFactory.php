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

    /**
     * @param string      $name
     * @param string|null $visibility
     */
    public function __construct($name, $visibility = null)
    {
        $this->name       = $name;
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
     * @return string
     */
    public function generate()
    {
        $template = '%visibility%function %name%() {}';

        return str_replace(['%visibility%', '%name%'], [$this->generateVisibility(), $this->getName()], $template);
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
}
