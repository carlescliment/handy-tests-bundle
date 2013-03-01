<?php

namespace BladeTester\HandyTestsBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use BladeTester\HandyTestsBundle\Exception as Exceptions;

class FactoryGirl {

    private $namespace;
    private $om;

    public function __construct($namespace, ObjectManager $om)
    {
        $this->namespace = $namespace;
        $this->om = $om;
    }

    public function build($instance_name, array $attributes = array())
    {
        $factory = $this->getFactoryFor($instance_name);
        return $factory->build($attributes);
    }

    public function create($instance_name, array $attributes = array())
    {
        $factory = $this->getFactoryFor($instance_name);
        return $factory->create($attributes);
    }


    private function getFactoryFor($instance_name) {
        $this->assertNamespaceIsDefined();
        $factory_class = $this->getFactoryFullNameFor($instance_name);
        $this->assertFactoryExists($factory_class);
        return new $factory_class($this->om);
    }


    private function assertNamespaceIsDefined()
    {
        if (!$this->namespace) {
            throw new Exceptions\UndefinedNamespaceException('Namespace has not been defined.');
        }
    }

    private function assertFactoryExists($factory_class)
    {
        if (!class_exists($factory_class)) {
            throw new Exceptions\UndefinedFactoryException("No factory found in $factory_class");
        }
    }

    private function getFactoryFullNameFor($class)
    {
        return $this->namespace . '\\' . $class . 'Factory';
    }

}
