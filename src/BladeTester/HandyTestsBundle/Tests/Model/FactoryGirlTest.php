<?php

namespace BladeTester\HandyTestsBundle\Tests\Model;

use BladeTester\HandyTestsBundle\Model\FactoryGirl,
    BladeTester\HandyTestsBundle\Model\FactoryInterface;

class FactoryGirlTest extends \PHPUnit_Framework_TestCase {

    private $existingNamespace = 'BladeTester\HandyTestsBundle\Factory';
    private $existingClass = 'Sample';

    /**
     * @test
     * @expectedException BladeTester\HandyTestsBundle\Exception\UndefinedNamespaceException
     */
    public function itThrowsAnExceptionIfNamespaceIsNotDefined() {
        // Arrange
        $factory_girl = new FactoryGirl(null);

        // Expect (exception)

        // Act
        $factory_girl->build('Sample');
    }

    /**
     * @test
     * @expectedException BladeTester\HandyTestsBundle\Exception\UndefinedFactoryException
     */
    public function itThrowsAnExceptionIfFactoryIsNotDefined() {
        // Arrange
        $factory_girl = new FactoryGirl($this->existingNamespace);

        // Expect (exception)

        // Act
        $factory_girl->build('UnexistingFactory');
    }

    /**
     * @test
     */
    public function itBuildsAnInstanceIfClassExists() {
        // Arrange
        $factory_girl = new FactoryGirl($this->existingNamespace);

        // Act
        $class = $factory_girl->build($this->existingClass);

        // Expect (exception)
        $expectedType = 'BladeTester\HandyTestsBundle\Entity\Sample';
        $this->assertEquals($expectedType, get_class($class));
    }

    /**
     * @test
     */
    public function itCreatesAnInstanceIfClassExists() {
        // Arrange
        $factory_girl = new FactoryGirl($this->existingNamespace);

        // Act
        $class = $factory_girl->create($this->existingClass);

        // Expect (exception)
        $expectedType = 'BladeTester\HandyTestsBundle\Entity\Sample';
        $this->assertEquals($expectedType, get_class($class));
    }
}