<?php

namespace BladeTester\HandyTestsBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;
use BladeTester\HandyTestsBundle\Exception as Exceptions;

class FactoryGirl
{

    private $container;
    private $factoryServicesIds;

    public function __construct(ContainerInterface $container, array $factoryServicesIds)
    {
        $this->container = $container;
        $this->factoryServicesIds = $factoryServicesIds;
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
        $factory = $this->container->get($this->factoryServicesIds[$name]);
        $this->assertFactoryImplementsInterface($factory, $name);

        return $factory;
    }

    private function assertFactoryExists($name)
    {
        if (!isset($this->factoryServicesIds[$name])) {
            throw new Exceptions\UndefinedFactoryException(sprintf('The factory "%s" is not registered with the service container.', $name));
        }
    }

    private function assertFactoryImplementsInterface($factory, $name)
    {
        if (!$factory instanceof FactoryInterface) {
            throw new \RunTimeException(sprintf('The service `%s` must implement `FactoryInterface`', $name));
        }
    }
}
