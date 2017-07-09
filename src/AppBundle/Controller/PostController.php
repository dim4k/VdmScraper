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
		$posts = $this->get('doctrine.orm.entity_manager')
			->getRepository('AppBundle:Post')
			->findByDateAndAuthor($request->query->get('author'),$request->query->get('from'),$request->query->get('to'));

		/* @var $posts Post[] */
		$formatted = [];
		foreach ($posts as $post) {
			$formatted[] = [
				'id' => $post->getId(),
				'content' => $post->getContent(),
				'date' => $post->getDate()->format('Y-m-d H:i:s'),
				'author' => $post->getAuthor()
			];
		}

		$resObject = new \stdClass();
		$resObject->posts = $formatted;
		$resObject->count = count($formatted);

		$response = new Response(json_encode($resObject, JSON_UNESCAPED_UNICODE));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

	public function getPostAction($id, Request $request)
	{
		$post = $this->get('doctrine.orm.entity_manager')
			->getRepository('AppBundle:Post')
			->find($id);
		/* @var $post Post */

		if (empty($post)) {
			return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
		}

		$formatted = [
			'id' => $post->getId(),
			'content' => $post->getContent(),
			'date' => $post->getDate()->format('Y-m-d H:i:s'),
			'author' => $post->getAuthor()
		];

		$response = new Response(json_encode($formatted, JSON_UNESCAPED_UNICODE));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
}
