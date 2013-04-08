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
The Factory Girl allows you to easily instantiate and persist entities.

First, you need a directory where you can put your factories. This is why the `handy_tests.factory_girl` is for. In the path you define as param, build your own Factories.

This is an example of a factory:

    namespace Your\MainBundle\Factory;
    
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


Then, in your test, you can instantiate the factory girl:


    $factory_girl = $client->getKernel()->getContainer()->get('handy_tests.factory_girl')
    $sample = $factory_girl->create('Sample');

Having a single place where instantiate or persist entities helps you to mantain your code and allows building complex instances with default values.


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
        public function handyFeatures() {
            // Use your factories to build or create entities.
            $complex_entity = $this->create('ComplexEntity', array('name' => 'sampleName')); // persisted
            $complex_entity = $this->build('ComplexEntity', array('name' => 'sampleName'));  // not persisted

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
* Contributor: [Pedro Nofuentes][pedronofuentes]


## Contribute and feedback

Please, feel free to provide feedback of this bundle. Contributions will be much appreciated.



[carlescliment]: https://github.com/carlescliment
[pedronofuentes]: https://github.com/pedronofuentes
[stubchainer]: https://github.com/carlescliment/BladeTester/tree/master/ChainedStubsBundle
