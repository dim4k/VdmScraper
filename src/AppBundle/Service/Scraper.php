<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Goutte\Client;

class Scraper
{
	private $url;

	private $posts;

	private $postLimit;

	public function __construct()
	{
		$this->url = "http://www.viedemerde.fr/";
		$this->postLimit = 5;
		$this->posts = array();
	}

	public function scrap()
	{
		$client = new Client();

		$page = 1;
		while(count($this->posts) < $this->postLimit)
		{
			$crawler = $client->request('GET', 'http://www.viedemerde.fr?page='.$page);
			$this->scrapPage($crawler);
			$page++;
		}
		return $this;
	}

	private function scrapPage(Crawler $crawler)
	{
		$crawler->filter('article')->each(function (Crawler $node, $i) {

			if($node->filter('.text-center')->count()) {
				// Check if post contains text (not only an image)
				if(str_replace("\n","",$node->filter( 'p')->text()) != "") {
					$post = new Post();

					// GET CONTENT
					$post->setContent(str_replace("\n", "", $node->filter('p')->text()));

					// GET AUTHOR
					$postInfos = str_replace(array("/", "\n"), "", $node->filter('.text-center')->text());
					$postInfos = substr($postInfos, 4);

					$arr = explode(" - ", $postInfos);

					// Sometimes there is empty Author so we're changing it to 'Anonyme'
					if(substr($arr[0], 0, 1) != '-') {
						$post->setAuthor($arr[0]);
					}else{
						$post->setAuthor('Anonyme');
					}

					// GET DATE
					if(substr($arr[0], 0, 1) != '-') {
						$post->setDate($this->formatDate($arr[1]));
					}else{
						$post->setDate($this->formatDate(substr($arr[0], 2)));
					}

					// ADD POST (if limit is not reach)
					if(count($this->posts) < $this->postLimit) {
						$this->posts[] = $post;
					}
				}
			}
		});
	}

	private function formatDate($date){
		$mois = array('janvier'=>'1',
			'février'=>'2',
			'mars'=>'3',
			'avril'=>'4',
			'mai'=>'5',
			'juin'=>'6',
			'juillet'=>'7',
			'aout'=>'8',
			'septembre'=>'9',
			'octobre'=>'10',
			'novembre'=>'11',
			'décembre'=>'12');

		$arr = explode(" ", $date);

		// There might be a blank char du to some gender emoji we need to increment from one more our index
		$i = $arr[0] == ' ' ? 0:1;
		$dateFormat = new \DateTime($arr[3+$i] . '-' . $mois[$arr[2+$i]] . '-' . $arr[1+$i]);

		$time = explode(":", $arr[4+$i]);

		$dateFormat->setTime($time[0], $time[1]);

		return $dateFormat;

	}

	public function getPosts()
	{
		return $this->posts;
	}
}