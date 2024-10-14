<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\Channel\ChannelInterface;
use App\Message\Handler;
use App\Message\Notification;
use App\Parser\ContentInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
	private Handler $handler;

	private MockObject&ChannelInterface $channelOneMock;

	private MockObject&ChannelInterface $channelTwoMock;

	protected function setUp(): void
	{
		$this->channelOneMock = $this->createMock(ChannelInterface::class);
		$this->channelTwoMock = $this->createMock(ChannelInterface::class);
		$this->channelOneMock->method('name')->willReturn('one');
		$this->channelTwoMock->method('name')->willReturn('two');
		$this->handler = new Handler([$this->channelOneMock, $this->channelTwoMock]);
	}

	public function testInvokeSendsMessageToCorrectChannel(): void
	{
		$notification = $this->createNotification(['one', 'three']);

		$this->channelOneMock
			->expects($this->once())
			->method('send')
			->with($notification);

		$this->channelTwoMock
			->expects($this->never())
			->method('send');

		($this->handler)($notification);
	}

	public function testInvokeDoesNotSendMessageIfChannelIsNotSupported(): void
	{
		$notification = $this->createNotification(['three', 'four']);

		$this->channelOneMock
			->expects($this->never())
			->method('send');

		$this->channelTwoMock
			->expects($this->never())
			->method('send');

		($this->handler)($notification);
	}

	public function testInvokeSendsMessageToMultipleChannels(): void
	{
		$notification = $this->createNotification(['one', 'two']);

		$this->channelOneMock
			->expects($this->once())
			->method('send')
			->with($notification);

		$this->channelTwoMock
			->expects($this->once())
			->method('send')
			->with($notification);

		($this->handler)($notification);
	}

	/**
	 * @param string[] $channels
	 */
	private function createNotification(array $channels): Notification
	{
		return new Notification(
			$this->createMock(ContentInterface::class),
			$channels,
		);
	}
}
