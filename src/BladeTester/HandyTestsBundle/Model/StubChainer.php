<?php

namespace BladeTester\HandyTestsBundle\Model;

class StubChainer {

    private $testCase;

    public function __construct(\PHPUnit_Framework_TestCase $testCase) {
        $this->testCase = $testCase;
    }

    public function chain($stub, $methods, $value) {
        $values = array($value);
        for ($i = count($methods)-1; $i>0; $i--) {
            $method = $methods[$i];
            $value = $values[count($methods) - ($i+1)];
            $values[] = $this->createIntermediateStub($method, $value);
        }
        $this->stub($stub, $methods[0], $values[count($values)-1]);
    }

    private function createIntermediateStub($method, $value) {
        $transitionStub = $this->testCase->getMock("transitionStub", array($method));
        $this->stub($transitionStub, $method, $value);
        return $transitionStub;
    }

    private function stub($stub, $method, $value) {
        $stub->expects($this->testCase->any())
            ->method($method)
            ->will($this->testCase->returnValue($value));
    }
}
