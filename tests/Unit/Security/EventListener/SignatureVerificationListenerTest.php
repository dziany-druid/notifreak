<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\EventListener;

use App\Security\EventListener\SignatureVerificationListener;
use App\Security\Signature\SignatureInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SignatureVerificationListenerTest extends TestCase
{
	private MockObject&SignatureInterface $signatureMock;

	private SignatureVerificationListener $listener;

	private MockObject&HttpKernelInterface $kernelMock;

	private MockObject&TranslatorInterface $translatorMock;

	protected function setUp(): void
	{
		$this->signatureMock = $this->createMock(SignatureInterface::class);
		$this->kernelMock = $this->createMock(HttpKernelInterface::class);
		$this->translatorMock = $this->createMock(TranslatorInterface::class);
		$this->listener = new SignatureVerificationListener($this->signatureMock, $this->translatorMock, false);
	}

	public function testOnKernelRequestWithInvalidRoute(): void
	{
		$request = $this->createRequest('signature', 'parser_name', 'some_other_route');
		$event = $this->createEvent($request);
		$this->listener->onKernelRequest($event);
		$this->assertNull($event->getResponse());
	}

	public function testOnKernelRequestWithMissingSignature(): void
	{
		$request = $this->createRequest(null, 'parser_name');
		$event = $this->createEvent($request);
		$this->listener->onKernelRequest($event);
		$this->assertForbiddenResponse($event->getResponse());
	}

	public function testOnKernelRequestWithMissingParser(): void
	{
		$request = $this->createRequest('signature', null);
		$event = $this->createEvent($request);
		$this->listener->onKernelRequest($event);
		$this->assertForbiddenResponse($event->getResponse());
	}

	public function testOnKernelRequestWithInvalidSignature(): void
	{
		$request = $this->createRequest('signature', 'parser_name');
		$event = $this->createEvent($request);
		$this->signatureMock->method('verify')->willReturn(false);
		$this->listener->onKernelRequest($event);
		$this->assertForbiddenResponse($event->getResponse());
	}

	public function testOnKernelRequestWithUnsafeSignature(): void
	{
		$request = $this->createRequest('unsafe', 'parser_name');
		$event = $this->createEvent($request);
		$this->listener->onKernelRequest($event);
		$this->assertForbiddenResponse($event->getResponse());
	}

	public function testOnKernelRequestWithValidSignature(): void
	{
		$request = $this->createRequest('signature', 'parser_name');
		$event = $this->createEvent($request);
		$this->signatureMock->method('verify')->willReturn(true);
		$this->listener->onKernelRequest($event);
		$this->assertNull($event->getResponse());
	}

	public function testOnKernelRequestWithUnsafeSignatureInDebugMode(): void
	{
		$listener = new SignatureVerificationListener($this->signatureMock, $this->translatorMock, true);
		$request = $this->createRequest('unsafe', 'parser_name');
		$event = $this->createEvent($request);
		$listener->onKernelRequest($event);
		$this->assertNull($event->getResponse());
	}

	private function createRequest(mixed $signature, mixed $parserName, string $route = 'queue_notification'): Request
	{
		$request = new Request();
		$request->attributes->set('_route', $route);
		$request->query->set('query_param', ['one', 'two']);

		if (null !== $signature) {
			$request->attributes->set('signature', $signature);
		}

		if (null !== $parserName) {
			$request->attributes->set('parserName', $parserName);
		}

		return $request;
	}

	private function createEvent(Request $request): RequestEvent
	{
		return new RequestEvent($this->kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
	}

	private function assertForbiddenResponse(?Response $response): void
	{
		$this->assertInstanceOf(JsonResponse::class, $response);
		$this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
	}
}
