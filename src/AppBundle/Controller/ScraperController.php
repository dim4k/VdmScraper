<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Service\Scraper;
use Goutte\Client;

/**
 * Scraper controller.
 *
 */
class ScraperController extends Controller
{
	/**
	 * @Route("/scraper", name="scraper")
	 */
	public function ScrapAction(Request $request)
	{
		$scraper = new Scraper();

		$posts = $scraper->scrap()->getPosts();

		foreach($posts as $post) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($post);
			$em->flush();
			$em->clear();
		}

		return new JsonResponse($posts);
	}

}
