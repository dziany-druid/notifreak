<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController
{
	#[Route(
		path: '/notification/{signature}/{service}',
		name: 'queue_notification',
		requirements: ['signature' => '[a-z0-9]+', 'service' => '[a-z]+'],
		methods: [Request::METHOD_POST],
		stateless: true,
	)]
	public function queue(string $service, Request $request): Response
	{
		$notifiers = $request->query->all('notifiers');

		return new JsonResponse(
			[
				'message' => 'The message has been added to the queue and will be sent through the following notification channels: "'.implode('", "', $notifiers).'".',
			],

			Response::HTTP_ACCEPTED,
		);
	}
}
