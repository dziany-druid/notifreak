<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\Formatter\ParserFactory;
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
		private readonly ParserFactory $parserFactory,
	) {
	}

	#[Route(
		path: '/notification/{signature}/{parserName}',
		name: 'queue_notification',
		requirements: ['signature' => '[a-z0-9]+', 'parser' => '[a-z]+'],
		methods: [Request::METHOD_POST],
		stateless: true,
	)]
	public function queue(string $parserName, Request $request): Response
	{
		$requestBody = $request->getContent();

		if (empty($requestBody)) {
			return $this->json(
				[
					'violations' => [
						[
							'message' => 'Request body must not be empty.',
						],
					],
				],

				Response::HTTP_BAD_REQUEST,
			);
		}

		$parser = $this->parserFactory->create($parserName);

		try {
			$content = $parser->parse($requestBody);
		} catch (\JsonException) {
			return $this->json(
				[
					'violations' => [
						[
							'message' => 'Request body is not a valid JSON.',
						],
					],
				],

				Response::HTTP_BAD_REQUEST,
			);
		}

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
