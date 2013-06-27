<?php

namespace BladeTester\HandyTestsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

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

        foreach ($container->findTaggedServiceIds('handy_tests.factory') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;

            // Flip, because we want tag aliases (= factory identifiers) as keys
            $factories[$alias] = $serviceId;
        }

        $definition->replaceArgument(1, $factories);
    }
}
