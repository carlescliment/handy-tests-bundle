<?php

namespace BladeTester\HandyTestsBundle\Model;

interface FactoryInterface {

    public function build(array $attributes);
    public function create(array $attributes);
}