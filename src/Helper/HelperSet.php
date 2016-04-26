<?php

/*
 * This file is part of the kreait eZ Publish Migrations Bundle.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kreait\EzPublish\MigrationsBundle\Helper;

use Kreait\EzPublish\MigrationsBundle\Helper;

class HelperSet implements \IteratorAggregate
{
    /**
     * @var Helper[]
     */
    private $helpers = [];

    public function __construct(array $helpers = [])
    {
        foreach ($helpers as $alias => $helper) {
            $this->set($helper, is_int($alias) ? null : $alias);
        }
    }

    public function set(Helper $helper, $alias = null)
    {
        $this->helpers[$helper->getName()] = $helper;
        if ($alias) {
            $this->helpers[$alias] = $helper;
        }

        $helper->setHelperSet($this);
    }

    public function has($name)
    {
        return array_key_exists($name, $this->helpers);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The helper "%s" is not defined.', $name));
        }

        return $this->helpers[$name];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->helpers);
    }
}
