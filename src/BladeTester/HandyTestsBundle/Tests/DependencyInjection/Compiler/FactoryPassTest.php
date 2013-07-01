<?php

namespace BladeTester\HandyTestsBundle\Tests\DependencyInjection\Compiler;

use BladeTester\HandyTestsBundle\DependencyInjection\Compiler\FactoryPass;

class FactoryPassTest extends \PHPUnit_Framework_TestCase
{

    private $factoryPass;
    private $container;
    private $factoryDefinition;


    public function setUp() {
        $this->factoryPass = new FactoryPass;
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->factoryDefinition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
    }


    /**
     * @test
     */
    public function itChecksThatFactoryGirlIsDefined()
    {
        // Arrange

        // Expect
        $this->container->expects($this->once())
            ->method('hasDefinition')
            ->with('handy_tests.factory_girl');

        // Act
        $this->factoryPass->process($this->container);
    }


    /**
     * @test
     */
    public function itDoesNothingIfFactoryGirlIsNotDefined()
    {
        // Arrange
        $this->stubFactoryIsNotDefined();

        // Expect
        $this->container->expects($this->never())
            ->method('getDefinition');

        // Act
        $this->factoryPass->process($this->container);
    }


    /**
     * @test
     */
    public function itGetsTheFactoryTaggedServices()
    {
        // Arrange
        $this->stubFactoryIsDefined();
        $this->container->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->onConsecutiveCalls(
                $this->factoryDefinition
                ));

        // Expect
        $this->container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('handy_tests.factory')
            ->will($this->returnValue(array()));

        // Act
        $this->factoryPass->process($this->container);
    }


    /**
     * @test
     */
    public function itGetsTheFactoryIfDefined()
    {
        // Arrange
        $this->stubFactoryIsDefined();
        $this->stubTaggedServices(array());

        // Expect
        $this->container->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($this->factoryDefinition));

        // Act
        $this->factoryPass->process($this->container);
    }


    /**
     * @test
     */
    public function itAddsEachFactoryToFactoryGirl()
    {
        // Arrange
        $this->stubFactoryIsDefined();
        $this->stubTaggedServices(array('factory', 'another_factory'));
        $this->container->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->onConsecutiveCalls(
                $this->factoryDefinition,
                $this->getDefinitionForClass('BladeTester\HandyTestsBundle\Tests\Model\SampleFactory'),
                $this->getDefinitionForClass('BladeTester\HandyTestsBundle\Tests\Model\SampleFactory')
                ));

        // Expect
        $this->factoryDefinition->expects($this->exactly(2))
            ->method('addMethodCall');

        // Act
        $this->factoryPass->process($this->container);
    }



    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itThrowsAnExceptionIfFactoryDoesNotImplementInterface()
    {
        // Arrange
        $this->stubFactoryIsDefined();
        $this->stubTaggedServices(array('factory'));
        $this->container->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->onConsecutiveCalls(
                $this->factoryDefinition,
                $this->getDefinitionForClass('stdClass')
                ));

        // Act
        $this->factoryPass->process($this->container);
    }


    private function stubFactoryIsNotDefined()
    {
        $this->stubFactoryDefinitionStatus(false);
    }


    private function stubFactoryIsDefined()
    {
        $this->stubFactoryDefinitionStatus(true);
    }


    private function stubFactoryDefinitionStatus($status)
    {
        $this->container->expects($this->any())
            ->method('hasDefinition')
            ->with('handy_tests.factory_girl')
            ->will($this->returnValue($status));
    }


    private function stubTaggedServices($services)
    {
        $this->container->expects($this->any())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));
    }


    private function getDefinitionForClass($class)
    {
        $definition_mock = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $definition_mock->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue($class));
        $this->container->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue($definition_mock));
        return $definition_mock;

    }

}
