<?php

namespace BladeTester\HandyTestsBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use BladeTester\HandyTestsBundle\DependencyInjection\Compiler\FactoryPass;

class BladeTesterHandyTestsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FactoryPass());
    }
}
