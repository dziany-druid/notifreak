<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util;

use App\Security\Signature\SignatureInterface;
use App\Util\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

final class UrlGeneratorTest extends TestCase
{
	private MockObject&RouterInterface $router;

	private MockObject&SignatureInterface $signature;

	private UrlGenerator $urlGenerator;

	protected function setUp(): void
	{
		$this->router = $this->createMock(RouterInterface::class);
		$this->signature = $this->createMock(SignatureInterface::class);
		$this->urlGenerator = new UrlGenerator($this->router, $this->signature);
	}

	public function testGenerateUrlWithValidChannels(): void
	{
		$parserName = 'testParser';
		$channels = ['channel_one', 'channel_two'];
		$signature = 'signature';
		$expectedUrl = 'http://example.com/notification/'.$signature;

		$this->signature->expects($this->once())
			->method('generate')
			->with($parserName.serialize(['channels' => $channels]))
			->willReturn($signature);

		$this->router
			->expects($this->once())
			->method('generate')
			->with(
				'queue_notification',
				[
					'signature' => $signature,
					'parserName' => $parserName,
				],
				RouterInterface::ABSOLUTE_URL,
			)
			->willReturn($expectedUrl);

		$url = $this->urlGenerator->generate($parserName, $channels);
		$this->assertEquals($expectedUrl.'?channels%5B0%5D=channel_one&channels%5B1%5D=channel_two', $url);
	}

	public function testGenerateUrlThrowsExceptionOnEmptyChannels(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Channels must not be empty.');
		$this->urlGenerator->generate('testParser', []);
	}
}
