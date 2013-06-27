<?php

namespace BladeTester\HandyTestsBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;
use BladeTester\HandyTestsBundle\Exception as Exceptions;

class FactoryGirl
{
    private $factories;

    public function __construct(array $factories = array())
    {
        $this->factories = array();
        foreach ($factories as $factory) {
            $this->addFactory($factory);
        }
    }

    public function addFactory(FactoryInterface $factory)
    {
        $this->factories[$factory->getName()] = $factory;
    }

    public function build($name, array $attributes = array())
    {
        $factory = $this->getFactoryFor($name);

        return $factory->build($attributes);
    }

    public function create($name, array $attributes = array())
    {
        $factory = $this->getFactoryFor($name);

        return $factory->create($attributes);
    }

    private function getFactoryFor($name)
    {
        $this->assertFactoryExists($name);
        $factory = $this->factories[$name];

        return $factory;
    }

    private function assertFactoryExists($name)
    {
        if (!isset($this->factories[$name])) {
            throw new Exceptions\UndefinedFactoryException(sprintf('The factory "%s" is not registered with the service container.', $name));
        }
    }
}
