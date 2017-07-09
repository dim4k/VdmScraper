<?php

// tests/AppBundle/Command/CreateUserCommandTest.php
namespace Tests\AppBundle\Command;

use AppBundle\Command\ScrapVdmCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AppBundleCommandTest extends KernelTestCase
{
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