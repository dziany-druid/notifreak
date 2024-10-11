<?php

declare(strict_types=1);

namespace App\Security\EventListener;

use App\Security\Signature\SignatureInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(
	event: 'kernel.request',
	method: 'onKernelRequest',
)]
final class SignatureVerificationListener
{
	public function __construct(
		private readonly SignatureInterface $signature,

		#[Autowire(param: 'kernel.debug')]
		private readonly bool $debugModeEnabled,
	) {
	}

	public function onKernelRequest(RequestEvent $event): void
	{
		$request = $event->getRequest();

		if ('queue_notification' !== $request->attributes->get('_route')) {
			return;
		}

		$signature = $request->attributes->get('signature');
		$service = $request->attributes->get('service');

		if (!\is_string($signature) || !\is_string($service)) {
			$this->forbiddenResponse($event);

			return;
		}

		if ('unsafe' === $signature && $this->debugModeEnabled) {
			return;
		}

		$queryParams = $request->query->all();

		if (!$this->signature->verify($signature, $service.serialize($queryParams))) {
			$this->forbiddenResponse($event);
		}
	}

	private function forbiddenResponse(RequestEvent $event): void
	{
		$event->setResponse(
			new JsonResponse(
				[
					'message' => 'Invalid signature.',
				],

				Response::HTTP_FORBIDDEN,
			),
		);

		$event->stopPropagation();
	}
}
