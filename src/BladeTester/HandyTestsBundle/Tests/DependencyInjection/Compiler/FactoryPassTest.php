<?php

namespace BladeTester\HandyTestsBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use BladeTester\HandyTestsBundle\DependencyInjection\Compiler\FactoryPass;

class FactoryPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itThrowsAnExceptionIfFactoryDoesNotImplementInterface()
    {
        // one service, not implementing any interface
        $services = array(
            'my_factory' => array(),
        );

        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $definition->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue('stdClass'));

        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(true));

        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));

        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $pass = new FactoryPass();
        $pass->process($builder);
    }

    public function testValidContentRenderer()
    {
        $services = array(
            'my_factory' => array(),
        );

        $renderer = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $renderer
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('addFactory', array(new Reference('my_factory')))
        ;

        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $definition->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue('BladeTester\HandyTestsBundle\Tests\Model\SampleFactory'));

        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(true));

        // We don't test kernel.fragment_renderer here
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));

        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->onConsecutiveCalls($renderer, $definition));

        $pass = new FactoryPass();
        $pass->process($builder);
    }
}
