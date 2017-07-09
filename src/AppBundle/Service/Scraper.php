<?php

namespace AppBundle\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Goutte\Client;

class Scraper
{
	private $url;

	private $posts;

	private $nbPosts;

	public function __construct()
	{
		$this->url = "http://www.viedemerde.fr/";
		$this->nbPosts = 200;
		$this->posts = array();
	}

	public function scrap()
	{
		$client = new Client();

			$client->request('GET', 'http://www.viedemerde.fr')->filter('article')->each(function (Crawler $node, $i) {

				if( $node->filter('.text-center')->count() ) {
					var_dump(str_replace("\n","",$node->filter( 'p')->text()));
					var_dump(str_replace(array("/","\n"), "", $node->filter('.text-center')->text()));
				}
			});

		return $this;
	}

	public function getPosts()
	{
		return $this->posts;
	}
}