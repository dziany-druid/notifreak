<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\Notification;
use App\Parser\ParserInterface;
use App\Parser\Request\ParsedRequest;
use App\Parser\Request\ParsedRequestBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class NotificationController extends AbstractController
{
	/**
	 * @param ParserInterface[] $parsers
	 */
	public function __construct(
		private readonly MessageBusInterface $messageBus,

		#[AutowireIterator('app.request.parser')]
		private readonly iterable $parsers,
	) {
	}

	#[Route(
		path: '/notification/{signature}/{parserName}',
		name: 'queue_notification',
		requirements: ['signature' => '[a-z0-9]+', 'parserName' => '[a-z]+'],
		methods: [Request::METHOD_POST],
		stateless: true,
	)]
	public function queue(Request $request): Response
	{
		$parsedRequest = $this->parseRequest($request);

		if (\count($parsedRequest->violations) > 0) {
			return $this->json(
				[
					'violations' => $this->getViolationMessages($parsedRequest->violations),
				],

				Response::HTTP_BAD_REQUEST,
			);
		}

		$notification = new Notification($parsedRequest->content, $parsedRequest->queryParams->channels);

		/** @var Notification $sentMessage */
		$sentMessage = $this->messageBus->dispatch($notification)->getMessage();

		return $this->json(
			[
				'content' => $sentMessage->plainContent,
				'channels' => $sentMessage->channels,
			],

			Response::HTTP_ACCEPTED,
		);
	}

	private function parseRequest(Request $request): ParsedRequest
	{
		$parsedRequestBuilder = new ParsedRequestBuilder($request);

		foreach ($this->parsers as $parser) {
			if ($parser->supports($request)) {
				$parser->parse($request, $parsedRequestBuilder);
			}
		}

		return $parsedRequestBuilder->build();
	}

	/**
	 * @return array<int, array{
	 *     propertyPath: string,
	 *     message: string|\Stringable
	 * }>
	 */
	private function getViolationMessages(ConstraintViolationListInterface $violations): array
	{
		$violationMessages = [];

		foreach ($violations as $violation) {
			$violationMessages[] = [
				'propertyPath' => $violation->getPropertyPath(),
				'message' => $violation->getMessage(),
			];
		}

		return $violationMessages;
	}
}
