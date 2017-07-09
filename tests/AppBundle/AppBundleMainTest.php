<?php

namespace AppBundle\Tests;

use AppBundle\Controller\PostController;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Command\ScrapVdmCommand;
use Symfony\Component\Console\Tester\CommandTester;


class AppBundleMainTests extends WebTestCase
{
	/**
	 * @var EntityManager
	 */
	private $_em;

	private $application;

	// Initialize test database and some test data
	protected function setUp()
	{
		static::$kernel = static::createKernel();
		static::$kernel->boot();

		$this->application = new Application(static::$kernel);

		// drop the database
		$command = new DropDatabaseDoctrineCommand();
		$this->application->add($command);
		$input = new ArrayInput(array(
			'command' => 'doctrine:database:drop',
			'--force' => true
		));
		$command->run($input, new NullOutput());

		// we have to close the connection after dropping the database so we don't get "No database selected" error
		$connection = $this->application->getKernel()->getContainer()->get('doctrine')->getConnection();
		if ($connection->isConnected()) {
			$connection->close();
		}

		// create the database
		$command = new CreateDatabaseDoctrineCommand();
		$this->application->add($command);
		$input = new ArrayInput(array(
			'command' => 'doctrine:database:create',
		));
		$command->run($input, new NullOutput());

		// create schema
		$command = new CreateSchemaDoctrineCommand();
		$this->application->add($command);
		$input = new ArrayInput(array(
			'command' => 'doctrine:schema:create',
		));
		$command->run($input, new NullOutput());

		// get the Entity Manager
		$this->_em = static::$kernel->getContainer()
			->get('doctrine')
			->getManager();

		$post1 = new Post();
		$date1 = new \DateTime('2017-01-01');
		$post1->setDate($date1);
		$post1->setContent('First content test of vdm scraper');
		$post1->setAuthor('Author1');
		$this->_em->persist($post1);

		$post2 = new Post();
		$date2 = new \DateTime('2017-07-10');
		$post2->setDate($date2);
		$post2->setContent('Content number 2 of vdm scraper');
		$post2->setAuthor('Author1');
		$this->_em->persist($post2);

		$post3 = new Post();
		$date3 = new \DateTime('2016-02-01');
		$post3->setDate($date3);
		$post3->setContent('Content number 3 of vdm scraper');
		$post3->setAuthor('Author2');
		$this->_em->persist($post3);

		$this->_em->flush();
		$this->_em->clear();

	}

	protected function tearDown()
	{
		parent::tearDown();
		$this->_em->close();
	}

	// Tests begin from here
	public function testHomepage()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/');

		$this->assertEquals(200, $client->getResponse()->getStatusCode(),'Unexpected status code response ');
		$this->assertContains('Welcome to VDM Web Scraper', $crawler->filter('#container h1')->text(),'Unexpected content response');
	}

	public function testGetPosts()
	{
		$client = $this->createClient();
		$client->request('GET', '/posts');

		$response = $client->getResponse();

		// Test if response is OK
		$this->assertSame(200, $client->getResponse()->getStatusCode(),'Unexpected status code response ');
		// Test if Content-Type is valid application/json
		$this->assertSame('application/json', $response->headers->get('Content-Type'),'Unexpected content type response');
		// Test response content fetch json format and test data
		$this->assertJsonStringEqualsJsonString($client->getResponse()->getContent(),
			'{"posts":[{"id":1,"content":"First content test of vdm scraper","date":"2017-01-01 00:00:00","author":"Author1"},{"id":2,"content":"Content number 2 of vdm scraper","date":"2017-07-10 00:00:00","author":"Author1"},{"id":3,"content":"Content number 3 of vdm scraper","date":"2016-02-01 00:00:00","author":"Author2"}],"count":3}',
			'Unexpected Json response');
	}

	public function testGetPostsWithParam()
	{
		$client = $this->createClient();

		// Test with dates parameters
		$client->request('GET', '/posts?from=2017-01-01&to=2017-09-01');

		$response = $client->getResponse();

		// Test if response is OK
		$this->assertSame(200, $client->getResponse()->getStatusCode());
		// Test if Content-Type is valid application/json
		$this->assertSame('application/json', $response->headers->get('Content-Type'));
		// Test response content fetch json format and test data
		$this->assertJsonStringEqualsJsonString($client->getResponse()->getContent(),
			'{"posts":[{"id":1,"content":"First content test of vdm scraper","date":"2017-01-01 00:00:00","author":"Author1"},{"id":2,"content":"Content number 2 of vdm scraper","date":"2017-07-10 00:00:00","author":"Author1"}],"count":2}',
			$client->getResponse()->getContent());

		// Test with author parameter
		$client->request('GET', '/posts?author=Author1');

		$response = $client->getResponse();

		// Test if response is OK
		$this->assertSame(200, $client->getResponse()->getStatusCode());
		// Test if Content-Type is valid application/json
		$this->assertSame('application/json', $response->headers->get('Content-Type'));
		// Test response content fetch json format and test data
		$this->assertJsonStringEqualsJsonString($client->getResponse()->getContent(),
			'{"posts":[{"id":1,"content":"First content test of vdm scraper","date":"2017-01-01 00:00:00","author":"Author1"},{"id":2,"content":"Content number 2 of vdm scraper","date":"2017-07-10 00:00:00","author":"Author1"}],"count":2}',
			'Unexpected Json response');
	}


	public function testGetByIdPost()
	{
		$client = $this->createClient();

		// Test with dates parameters
		$client->request('GET', '/posts/2');

		$response = $client->getResponse();

		// Test if response is OK
		$this->assertSame(200, $client->getResponse()->getStatusCode());
		// Test if Content-Type is valid application/json
		$this->assertSame('application/json', $response->headers->get('Content-Type'));
		// Test response content fetch json format and test data
		$this->assertJsonStringEqualsJsonString($client->getResponse()->getContent(),
			'{"id":2,"content":"Content number 2 of vdm scraper","date":"2017-07-10 00:00:00","author":"Author1"}',
			'Unexpected Json response');
	}

	public function testExecute()
	{
		self::bootKernel();
		$application = new Application(self::$kernel);

		$application->add(new ScrapVdmCommand());

		$command = $application->find('app:scrap-vdm');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'command'  => $command->getName(),

			// pass arguments to the helper
			'limit' => '15',
		));

		// the output of the command in the console
		$output = $commandTester->getDisplay();
		$this->assertContains('VDM SCRAPER', $output,'Unexpected output for the command scrap-vdm');
		$this->assertContains('Limit : 15', $output,'Unexpected output for the command scrap-vdm, limit is not take as parameter');
		$this->assertContains('Success !', $output,'Something went wrong during scraping');
	}

}
