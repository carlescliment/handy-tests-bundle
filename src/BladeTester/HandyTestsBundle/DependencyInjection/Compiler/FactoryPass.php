<?php

namespace BladeTester\HandyTestsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds all services with the tags "handy_tests.factory" as arguments
 * of the "handy_tests.factory_girl" service
 */
class FactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('handy_tests.factory_girl')) {
            return;
        }

        $definition = $container->getDefinition('handy_tests.factory_girl');

        // Builds an array with service IDs as keys and tag aliases as values
        $factories = array();

        foreach (array_keys($container->findTaggedServiceIds('handy_tests.factory')) as $id) {
            $class = $container->getDefinition($id)->getClass();

            $refClass = new \ReflectionClass($class);
            $interface = 'BladeTester\HandyTestsBundle\Model\FactoryInterface';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }

            $definition->addMethodCall('addFactory', array(new Reference($id)));
        }
    }
}
