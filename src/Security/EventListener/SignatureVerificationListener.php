<?php

declare(strict_types=1);

namespace App\Security\EventListener;

use App\Security\Signature\SignatureInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(
	event: 'kernel.request',
	method: 'onKernelRequest',
)]
final class SignatureVerificationListener
{
	public function __construct(
		private readonly SignatureInterface $signature,
		private readonly TranslatorInterface $translator,

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
		$parserName = $request->attributes->get('parserName');

		if (!\is_string($signature) || !\is_string($parserName)) {
			$this->forbiddenResponse($event);

			return;
		}

		if ('unsafe' === $signature && $this->debugModeEnabled) {
			return;
		}

		$queryParams = $request->query->all();

		if (!$this->signature->verify($signature, $parserName.serialize($queryParams))) {
			$this->forbiddenResponse($event);
		}
	}

	private function forbiddenResponse(RequestEvent $event): void
	{
		$event->setResponse(
			new JsonResponse(
				[
					'message' => $this->translator->trans('Invalid signature.'),
				],

				Response::HTTP_FORBIDDEN,
			),
		);

		$event->stopPropagation();
	}
}
