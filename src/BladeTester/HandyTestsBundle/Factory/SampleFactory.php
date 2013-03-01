<?php

namespace BladeTester\HandyTestsBundle\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use BladeTester\HandyTestsBundle\Model\FactoryInterface;

use BladeTester\HandyTestsBundle\Entity\Sample;

class SampleFactory implements FactoryInterface {

    private $om;

    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }

    public function build(array $attributes) {
        return new Sample;
    }

    public function create(array $attributes) {
        $sample = $this->build($attributes);
        $this->om->persist($sample);
        $this->om->flush();
        return $sample;
    }
}

