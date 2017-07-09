<?php

namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Service\Scraper;

class ScrapVdmCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this
			// the name of the command (the part after "bin/console")
			->setName('app:scrap-vdm')

			// the short description shown while running "php bin/console list"
			->setDescription('Extract post from viedemerde website and persist them into app database')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you extract post from viedemerde website and persist them into app database')

			->addArgument('limit', InputArgument::OPTIONAL, 'Set limit of post scraped (default 20)')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$postLimit = $input->getArgument('limit') != null || $input->getArgument('limit') != '' ? $input->getArgument('limit'):20;;

		// outputs multiple lines to the console (adding "\n" at the end of each line)
		$output->writeln([
			'',
			'=================================================================',
			'|                          VDM SCRAPER                          |',
			'| Limit : '.$postLimit.' (change limit by adding number after this command) |',
			'=================================================================',
			'',
		]);

		$scraper = new Scraper($postLimit);

		$output->writeln('Scraping posts...');
		$posts = $scraper->scrap()->getPosts();

		$output->writeln('Writing in database...');
		foreach($posts as $key=>$post) {
			$em = $this->getContainer()->get('doctrine')->getManager();
			$em->persist($post);
			$em->flush();
			$em->clear();
		}

		// outputs a message followed by a "\n"
		$output->writeln(['','Success !','']);
	}

}
