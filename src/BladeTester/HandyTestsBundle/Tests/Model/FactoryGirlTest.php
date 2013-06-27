<?php

namespace BladeTester\HandyTestsBundle\Tests\Model;

use BladeTester\HandyTestsBundle\Model\FactoryGirl,
    BladeTester\HandyTestsBundle\Model\FactoryInterface;

class FactoryGirlTest extends \PHPUnit_Framework_TestCase {

    private $existingClass;
    private $om;

    public function setUp() {
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->existingClass = 'Sample';
    }

    /**
     * @test
     * @expectedException BladeTester\HandyTestsBundle\Exception\UndefinedFactoryException
     */
    public function itThrowsAnExceptionIfFactoryIsNotDefined() {
        // Arrange
        $factory_girl = new FactoryGirl();

        // Expect (exception)

        // Act
        $factory_girl->build('UnexistingFactory');
    }

    /**
     * @test
     */
    public function itBuildsAnInstanceIfClassExists() {
        // Arrange
        $factory = new SampleFactory($this->om);
        $factory_girl = new FactoryGirl(array($factory));
        $expectedClass = 'BladeTester\HandyTestsBundle\Tests\Entity\Sample';

        // Act
        $sample = $factory_girl->build('sample');

        // Expect
        $this->assertEquals($expectedClass, get_class($sample));
    }

    /**
     * @test
     */
    public function itCreatesAnInstanceIfClassExists() {
        // Arrange
        $factory = new SampleFactory($this->om);
        $factory_girl = new FactoryGirl(array($factory));
        $expectedClass = 'BladeTester\HandyTestsBundle\Tests\Entity\Sample';

        // Act
        $sample = $factory_girl->create('sample');

        // Expect
        $this->assertEquals($expectedClass, get_class($sample));
    }

}