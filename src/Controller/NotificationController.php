<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\ContentInterface;
use App\Message\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationController extends AbstractController
{
	public function __construct(
		private readonly ValidatorInterface $validator,
		private readonly MessageBusInterface $messageBus,
	) {
	}

	#[Route(
		path: '/notification/{signature}/{service}',
		name: 'queue_notification',
		requirements: ['signature' => '[a-z0-9]+', 'service' => '[a-z]+'],
		methods: [Request::METHOD_POST],
		stateless: true,
	)]
	public function queue(string $service, Request $request): Response
	{
		$content = new class implements ContentInterface {
			public function plain(): string
			{
				return 'plain';
			}

			public function html(): string
			{
				return 'html';
			}

			public function markdown(): string
			{
				return 'markdown';
			}
		};

		$notification = new Notification(
			$content,
			$request->query->all('channels'),
		);

		$violations = $this->validator->validate($notification);

		if (\count($violations) > 0) {
			$violationMessages = [];

			foreach ($violations as $violation) {
				$violationMessages[] = [
					'propertyPath' => $violation->getPropertyPath(),
					'message' => $violation->getMessage(),
				];
			}

			return $this->json(
				[
					'violations' => $violationMessages,
				],

				Response::HTTP_BAD_REQUEST,
			);
		}

		/** @var Notification $sentMessage */
		$sentMessage = $this->messageBus->dispatch($notification)->getMessage();

		return $this->json(
			[
				'content' => $sentMessage->content->plain(),
				'channels' => $sentMessage->channels,
			],

			Response::HTTP_ACCEPTED,
		);
	}
}
