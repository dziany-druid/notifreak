<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Channel;

use App\Message\Channel\Telegram;
use App\Message\ContentInterface;
use App\Message\Notification;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class TelegramTest extends TestCase
{
	private MockObject&ChatterInterface $chatterMock;

	private Telegram $telegram;

	protected function setUp(): void
	{
		$this->chatterMock = $this->createMock(ChatterInterface::class);
		$this->telegram = new Telegram($this->chatterMock);
	}

	public function testNameReturnsCorrectChannelName(): void
	{
		$this->assertSame('telegram', $this->telegram->name());
	}

	public function testSendCreatesChatMessageAndSendsIt(): void
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$contentMock->method('markdown')->willReturn('Test message');
		$notification = new Notification($contentMock, ['telegram']);

		$this->chatterMock
			->expects($this->once())
			->method('send')
			->with(
				$this->callback(
					function (ChatMessage $chatMessage) use ($notification) {
						$options = (new TelegramOptions())
							->parseMode(TelegramOptions::PARSE_MODE_MARKDOWN)
							->disableWebPagePreview(true);

						$this->assertEquals($options, $chatMessage->getOptions());
						$this->assertSame($notification->content->markdown(), $chatMessage->getSubject());
						$this->assertSame('telegram', $chatMessage->getTransport());

						return true;
					},
				),
			);

		$this->telegram->send($notification);
	}

	public function testSendTrimsLongMessageAndSendsIt(): void
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$contentMock->method('markdown')->willReturn(str_repeat('A', 4100));
		$notification = new Notification($contentMock, ['telegram']);

		$this->chatterMock
			->expects($this->once())
			->method('send')
			->with(
				$this->callback(
					function (ChatMessage $chatMessage) {
						$this->assertSame(str_repeat('A', 4096), $chatMessage->getSubject());

						return true;
					},
				),
			);

		$this->telegram->send($notification);
	}
}
