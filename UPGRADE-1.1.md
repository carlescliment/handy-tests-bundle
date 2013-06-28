How to upgrade to 1.1
=====================


##General

The Factory Girl has been significantly improved with the great contributions of [@franmomu][franmomu]. If you don't use factories in your functional tests (you should), you can skip this reading.


##The old convention approach

In the 1.0 version of the FactoryGirl, many decision were made based on stablishing conventions. In order to create a factory of an entity, you wrote the name of the entity and FactoryGirl then looked for a class named "Entity" + "Factory" in the defined namespace. This approach had many flaws:

- You had to contain all your factories in the same namespace. This made difficult to decouple bundles and share them between projects.

- You had no control over dependency injection. It was the FactoryGirl itself who handled it. So if you needed another services in your factories, you had to create them in constructor or pass them to create/build method in the array.


##The new configuration approach
The new approach needs a bit more of configuration stuff, but adds a lot of flexibility. It uses a [Compiler Pass][compiler_pass] to collect all factories and pass them to FactoryGirl. To do that, you need to register your factories as services and tag them properly:


    services:
        your_vendor.handy_test.person_factory:
            class: Path\To\Your\Factory
            arguments: ["@doctrine.orm.entity_manager"]
            tags:
                - { name: handy_tests.factory }


After that, you have to implement the new method added to FactoryInterface:


    namespace Your\OwnBundle\Factory;

    use Doctrine\Common\Persistence\ObjectManager;
    use BladeTester\HandyTestsBundle\Model\FactoryInterface;

    use Your\OwnBundle\Entity\Person;

    class PersonFactory implements FactoryInterface {

        private $om;


        // ...


        public function getName()
        {
            return 'Person';
        }
    }


And that's it! If you have factories of different bundles, now you can define the proper namespace and move them. Also, if you have other dependencies (i.e. one factory that needs another) you can easily add them to the constructor arguments.



[franmomu]: https://github.com/franmomu
[compiler_pass]: http://symfony.com/doc/current/cookbook/service_container/compiler_passes.html
