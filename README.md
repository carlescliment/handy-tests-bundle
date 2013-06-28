HandyTestsBundle
==================

This is a collection of the tools I use daily to make testing easier. Feel free to use this toolset in your projects!




## Installation

### 1. Update your vendors

Add this line to your `composer.json`

    "require": {
        "carlescliment/handy-tests-bundle": "dev-master"
    }

Execute `php composer.phar update carlescliment/handy-tests-bundle`

### Important note:
Thanks to [@franmomu][franmomu], this bundle has been significantly improved. If you are currently using it and don't have time to make the changes needed, please stick to the previous version.

    "require": {
        "carlescliment/handy-tests-bundle": "1.0.x-dev"
    }


### 2. Load the bundle in `app/AppKernel.php`
    if ('test' === $this->getEnvironment()) {
        $bundles[] = new BladeTester\HandyTestsBundle\BladeTesterHandyTestsBundle();
    }

### 3. Modify your `app/config/config_test.yml`

    services:
        handy_tests.factory_girl:
            class: "BladeTester\HandyTestsBundle\Model\FactoryGirl"
            arguments: ['Your\MainBundle\Tests\Factory', "@doctrine.orm.entity_manager"]


## The Toolkit

### The table truncator
Many of you use fixtures to fill the database with appropriate data each test. But, sometimes, the schema is so complex that it gets very slow to always reload a big fixture file.

In these cases, you will probably prefer a more fine-grained approach, in which you create the instances you want, removing them from the database after the test is finished.

The table truncator allows you to -obvious- truncate tables (MySQL only).

    use BladeTester\HandyTestsBundle\Model\TableTruncator;

    $tables = array('table1', 'table2', 'table3');
    TableTruncator::truncate($tables, $entity_manager);


### The stub chainer
[Explained here][stubchainer]

### The Factory Girl
The Factory Girl allows you to easily instantiate and persist entities. Instantiating and persisting objects from a single place helps removing duplication and allows building complex instances with default values without generating noise.


This is an example of a factory:

    namespace Your\OwnBundle\Factory;

    use Doctrine\Common\Persistence\ObjectManager;
    use BladeTester\HandyTestsBundle\Model\FactoryInterface;

    use Your\OwnBundle\Entity\Person;

    class PersonFactory implements FactoryInterface {

        private $om;


        public function __construct(ObjectManager $om)
        {
            $this->om = $om;
        }


        public function getName()
        {
            return 'Person';
        }


        public function build(array $attributes)
        {
            $name = isset($attributes['name']) ? $attributes['name'] : 'Factorized name';
            $surname = isset($attributes['surname']) ? $attributes['surname'] : 'Factorized surname';
            $age = isset($attributes['age']) ? $attributes['age'] : null;
            $person = new Person;
            $person->setName($name);
            $person->setSurname($surname);
            $person->setAge($age);
            return $person;
        }


        public function create(array $attributes)
        {
            $person = $this->build($attributes);
            $this->om->persist($person);
            $this->om->flush();
            return $person;
        }
    }


Once you have written your factory, register it as a tagged service in order to make it available from your tests.

file: services.yml

    imports:
        - { resource: factories.yml }

    parameters:
        # ....

    services:
        # Your other stuff

file: factories.yml

    services:
        your_vendor.handy_test.person_factory:
            class: Your\OwnBundle\Factory\PersonFactory
            arguments: ["@doctrine.orm.entity_manager"]
            tags:
                - { name: handy_tests.factory }



Then, in your test, you can create Person instances cleanly:


    $factory_girl = $client->getKernel()->getContainer()->get('handy_tests.factory_girl')
    $person = $factory_girl->create('Person');


Or even easier if you extend the HandyTestCase in your tests (see later):

    $person = $this->create('Person');



### The Handy Test Case

This is a TestCase that provides all the features described above and a little more. Just inherit it in your functional test cases.


    namespace Your\Bundle\Tests\Controller;

    use BladeTester\HandyTestsBundle\Model\HandyTestCase;

    class FooControllerTest extends HandyTestCase {

        public function setUp() {
            parent::setUp(); // for annonymous users
            parent::setUp(array("PHP_AUTH_USER" => "test_user", "PHP_AUTH_PW"   => "test_password",)); // for basic http authentication
        }

        /**
         * @test
         */
        public function handyFeatures()
        {
            // Use factories to build or create entities.
            $persisted_entity = $this->create('ComplexEntity', array('name' => 'sampleName'));
            $not_persisted_entity = $this->build('ComplexEntity', array('name' => 'sampleName'));

            // Use the router instead of concrete paths
            $crawler = $this->visit('accout_show', array('id' => $account->getId()));

            // Perform XML HTTP requests
            $route_data = array('id' => 666); // to be used in the router
            $request_data = array('foo' => 'bar'); // things that go in $_POST or $_GET
            $crawler = $this->asyncRequest('my_service_route', $route_data, $request_data, 'POST');

            // debug the screen being displayed
            $this->printContents();

            // A handy entity manager
            $this->em->getRepository('...');

            // A handy client
            $this->client->click($link);

            // truncate tables
            $this->truncateTables(array('table_foo', 'table_bar'));

            // test your events properly without triggering them from interfaces
            $this->dispatchEvent('account.delete', $account_event);
        }

    }



## Credits

* Author: [Carles Climent][carlescliment]
* Contributor: [Fran Moreno][franmomu]
* Contributor: [Pedro Nofuentes][pedronofuentes]


## Contribute and feedback

Please, feel free to provide feedback of this bundle. Contributions will be much appreciated.



[carlescliment]: https://github.com/carlescliment
[franmomu]: https://github.com/franmomu
[pedronofuentes]: https://github.com/pedronofuentes
[stubchainer]: https://github.com/carlescliment/BladeTester/tree/master/ChainedStubsBundle
