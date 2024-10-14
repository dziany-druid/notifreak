<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\Notification;
use App\Parser\ContentInterface;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
	public function testNotificationConstructor(): void
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$channels = ['one', 'two'];
		$notification = new Notification($contentMock, $channels);
		$this->assertSame($contentMock, $notification->content);
		$this->assertSame($channels, $notification->channels);
	}
}
