<?php

namespace BladeTester\HandyTestsBundle\Factory;

use Doctrine\Common\Persitence\ObjectManager;
use BladeTester\HandyTestsBundle\Model\FactoryInterface;

use BladeTester\HandyTestsBundle\Entity\Sample;

class SampleFactory implements FactoryInterface {

    public function build(array $attributes) {
        return new Sample;
    }

    public function create(array $attributes) {

    }
}

