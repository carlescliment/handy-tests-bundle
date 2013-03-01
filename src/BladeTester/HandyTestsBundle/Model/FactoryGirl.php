<?php

namespace BladeTester\HandyTestsBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use BladeTester\HandyTestsBundle\Exception as Exceptions;

class FactoryGirl {

    private $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    public function build($instance_name)
    {
        $this->assertNamespaceIsDefined();
        $factory_class = $this->getFactoryFullNameFor($instance_name);
        $this->assertFactoryExists($factory_class);
        return $this->buildInstance($factory_class);
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

    private function buildInstance($factory_name)
    {
        $factory = new $factory_name();
        return $factory->build(array());
    }

}

/*
class FactoryGirl {

    private $om;
    private $namespace;

    public function __construct(ObjectManager $om, $namespace) {
        $this->om = $om;
        $this->namespace = $namespace;
    }

    public static function create($instance_name, $parameters = array()) {
        $class_name = $instance_name . 'Factory';
        $full_class_name = "VM\CRMBundle\Tests\Factory\\$class_name";
        return call_user_func_array(array($full_class_name, 'create'), array($this->em, $parameters));
    }
}
*/