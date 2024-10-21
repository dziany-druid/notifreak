<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Channel;

use App\Message\Channel\Telegram;
use App\Message\Notification;
use App\Parser\ContentInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final class TelegramTest extends TestCase
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
		$notification = $this->createNotification('Test message');

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
						$this->assertSame($notification->markdownContent, $chatMessage->getSubject());
						$this->assertSame('telegram', $chatMessage->getTransport());

						return true;
					},
				),
			);

		$this->telegram->send($notification);
	}

	public function testSendSplitsLongMessageAndSendsAllParts(): void
	{
		$longMessage = str_repeat('A', 4100);
		$notification = $this->createNotification($longMessage);

		/** @var array{0: string, 1: string} $sentMessages */
		$sentMessages = [];

		$this->chatterMock
			->expects($this->exactly(2))
			->method('send')
			->with(
				$this->callback(
					static function (ChatMessage $chatMessage) use (&$sentMessages) {
						$sentMessages[] = $chatMessage->getSubject();

						return true;
					},
				),
			);

		$this->telegram->send($notification);
		$this->assertCount(2, $sentMessages);
		$this->assertSame(str_repeat('A', 4096), $sentMessages[0]);
		$this->assertSame(str_repeat('A', 4), $sentMessages[1]);
	}

	public function testSendDoesNotSendWhenContentIsEmpty(): void
	{
		$notification = $this->createNotification('');

		$this->chatterMock
			->expects($this->never())
			->method('send');

		$this->telegram->send($notification);
	}

	private function createNotification(string $message): Notification
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$contentMock->method('plain')->willReturn($message);
		$contentMock->method('markdown')->willReturn($message);

		return new Notification($contentMock, ['telegram']);
	}
}
