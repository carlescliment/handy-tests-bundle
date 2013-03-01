<?php

namespace BladeTester\HandyTestsBundle\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class HandyTestCase extends WebTestCase {

    protected $em;
    protected $client;
    private $router;
    private $factoryGirl;
    private $dispatcher;


    public function setUp(array $auth = array()) {
        $this->client = self::createClient(array(), $auth);
        $this->initializeEntityManager();
        $this->router = $this->client->getKernel()->getContainer()->get('router');
        $this->factoryGirl = $this->client->getKernel()->getContainer()->get('handy_tests.factory_girl');
        $this->dispatcher = $this->container->get('event_dispatcher');
    }


    /**
     * Used to solve the "Too many connections" problem.
     * @see http://sf.khepin.com/2012/02/symfony2-testing-with-php-unit-quick-tip/
     */
    public function tearDown() {
        if ($this->em) {
            $this->em->getConnection()->close();
        }
        parent::tearDown();
    }


    private function initializeEntityManager() {
        $this->em = $this->client->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
    }


    protected function printContents() {
        print $this->client->getResponse()->getContent();
    }



    protected function visit($route_name, array $arguments = array()) {
        $route = $this->router->generate($route_name, $arguments);
        return $this->client->request('GET', $route);
    }


    protected function truncateTables($tables = array()) {
        TableTruncator::truncate($tables, $this->em);
    }

    protected function build($class_name, array $attributes = array()) {
        return $this->factoryGirl->build($class_name, $attributes);
    }

    protected function create($class_name, array $attributes = array()) {
        return $this->factoryGirl->create($class_name, $attributes);

    }

    protected function dispatchEvent($event_name, $event_class) {
        $this->dispatcher->dispatch($event_name, $event_class);
    }

}
