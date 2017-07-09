<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Post controller.
 */
class PostController extends Controller
{
	public function getPostsAction(Request $request)
	{
		$places = $this->get('doctrine.orm.entity_manager')
			->getRepository('AppBundle:Post')
			->findByDateAndAuthor($request->query->get('author'),$request->query->get('from'),$request->query->get('to'));
		/* @var $places Post[] */

		$formatted = [];
		foreach ($places as $place) {
			$formatted[] = [
				'id' => $place->getId(),
				'content' => $place->getContent(),
				'date' => $place->getDate(),
				'author' => $place->getAuthor()
			];
		}

		return new JsonResponse($formatted);
	}

	public function getPostAction(Request $request)
	{
		$place = $this->get('doctrine.orm.entity_manager')
			->getRepository('AppBundle:Post')
			->find($request->get('post_id'));
		/* @var $place Post */

		if (empty($place)) {
			return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
		}

		$formatted = [
			'id' => $place->getId(),
			'content' => $place->getContent(),
			'date' => $place->getDate(),
			'author' => $place->getAuthor()
		];

		return new JsonResponse($formatted);
	}
}
