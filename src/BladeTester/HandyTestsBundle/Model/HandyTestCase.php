<?php

namespace BladeTester\HandyTestsBundle\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class HandyTestCase extends WebTestCase
{

    protected $em;
    protected $client;
    private $router;
    private $factoryGirl;
    private $dispatcher;


    public function setUp(array $auth = array())
    {
        $this->client = self::createClient(array(), $auth);
        $this->initializeEntityManager();
        $this->router = $this->getService('router');
        $this->factoryGirl = $this->getService('handy_tests.factory_girl');
        $this->dispatcher = $this->getService('event_dispatcher');
    }

    public function getService($service_name) {
        return $this->getContainer()->get($service_name);
    }


    public function getParameter($parameter_name) {
        return $this->getContainer()->getParameter($parameter_name);
    }



    public function tearDown() {
        /**
         * Used to solve the "Too many connections" problem.
         * @see http://sf.khepin.com/2012/02/symfony2-testing-with-php-unit-quick-tip/
         */
        if ($this->em) {
            $this->em->getConnection()->close();
        }
        $this->em = null;
        $this->client = null;
        $this->router = null;
        $this->factoryGirl = null;
        $this->dispatcher = null;
        parent::tearDown();
    }


    private function initializeEntityManager() {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
    }


    protected function printContents()
    {
        print $this->client->getResponse()->getContent();
    }


    protected function visit($route_name, array $route_args = array(), $request_args = array(), $method = 'GET')
    {
        $route = $this->router->generate($route_name, $route_args);
        return $this->request($method, $route, $request_args);
    }


    protected function asyncRequest($route_name, array $route_args = array(), $request_args = array(), $method = 'GET', $headers = array())
    {
        $route = $this->router->generate($route_name, $route_args);
        $headers += array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        );
        return $this->request($method, $route, $request_args, $headers);
    }


    private function request($method, $route, $arguments = array(), $headers = array())
    {
        return $this->client->request($method, $route, $arguments, array(), $headers);
    }


    protected function truncateTables($tables = array(), $connection = 'default')
    {
        TableTruncator::truncate($tables, $this->getContainer()->get('doctrine')->getManager($connection));
    }


    protected function build($class_name, array $attributes = array())
    {
        return $this->factoryGirl->build($class_name, $attributes);
    }


    protected function create($class_name, array $attributes = array())
    {
        return $this->factoryGirl->create($class_name, $attributes);

    }


    protected function dispatchEvent($event_name, $event_class)
    {
        $this->dispatcher->dispatch($event_name, $event_class);
    }


    protected function assertJSONResponse($expected_response)
    {
        $json_response = $this->client->getResponse()->getContent();
        $decoded_response = json_decode($json_response, is_array($expected_response));
        $this->assertEquals($expected_response, $decoded_response);
    }

    private function getContainer()
    {
        return $this->client->getKernel()->getContainer();
    }

}
