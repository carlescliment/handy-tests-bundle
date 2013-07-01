<?php

namespace BladeTester\HandyTestsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds all services with the tags "handy_tests.factory" as arguments
 * of the "handy_tests.factory_girl" service
 */
class FactoryPass implements CompilerPassInterface
{

    const FACTORY_INTERFACE = 'BladeTester\HandyTestsBundle\Model\FactoryInterface';
    const FACTORY_GIRL_ID = 'handy_tests.factory_girl';
    const FACTORY_TAG = 'handy_tests.factory';


    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::FACTORY_GIRL_ID)) {
            $this->addDefinitionsToFactoryGirl($container);
        }
    }


    private function addDefinitionsToFactoryGirl(ContainerBuilder $container)
    {
        $factory_girl_definition = $container->getDefinition(self::FACTORY_GIRL_ID);
        $factory_definitions = $container->findTaggedServiceIds(self::FACTORY_TAG);
        foreach (array_keys($factory_definitions) as $id)
        {
            $class = $container->getDefinition($id)->getClass();
            if (!$this->implementsInterface($class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, self::FACTORY_INTERFACE));
            }
            $factory_girl_definition->addMethodCall('addFactory', array(new Reference($id)));
        }

    }


    private function implementsInterface($class)
    {
        $refClass = new \ReflectionClass($class);
        return $refClass->implementsInterface(self::FACTORY_INTERFACE);
    }
}
