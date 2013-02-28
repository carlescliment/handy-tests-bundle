<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

//require_once(__DIR__ . "/../../AppKernel.php");


class AutoCleanTestCase extends WebTestCase {

    protected $em;
    protected $client;
    private $router;


    public function setUp() {
        self::$kernel = new \AppKernel("test", true);
        self::$kernel->boot();
        $this->initializeEntityManager();
        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'test',
            'PHP_AUTH_PW'   => 'test',
        ));
        $this->client->followRedirects();
        $this->router = self::$kernel->getContainer()->get('router');
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
        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
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
}

?>
