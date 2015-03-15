<?php

namespace Cocur\Ea;

/**
 * Ea
 *
 * @package   Cocur\Ea
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 */
class Ea
{
    /** @var ClassFactory[] */
    protected $classes = [];

    /**
     * @return Ea
     */
    public static function create()
    {
        return new Ea();
    }

    /**
     * @param ClassFactory $class
     *
     * @return Ea
     */
    public function addClass(ClassFactory $class)
    {
        $this->classes[] = $class;

        return $this;
    }

    /**
     * @return ClassFactory[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @return string
     */
    public function generate()
    {
        return implode("\n", array_map(function ($class) { return $class->generate(); }, $this->getClasses()));
    }

    /**
     * @return Ea
     */
    public function execute()
    {
        eval($this->generate());

        return $this;
    }
}
