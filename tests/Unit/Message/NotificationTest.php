<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\Notification;
use App\Parser\ContentInterface;
use PHPUnit\Framework\TestCase;

final class NotificationTest extends TestCase
{
	public function testNotificationIsInitializedCorrectly(): void
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$plainContent = 'Plain content';
		$markdownContent = 'Markdown content';
		$htmlContent = 'HTML content';
		$contentMock->method('plain')->willReturn($plainContent);
		$contentMock->method('markdown')->willReturn($markdownContent);
		$contentMock->method('html')->willReturn($htmlContent);
		$channels = ['one', 'two'];
		$notification = new Notification($contentMock, $channels);
		$this->assertSame($plainContent, $notification->plainContent);
		$this->assertSame($markdownContent, $notification->markdownContent);
		$this->assertSame($htmlContent, $notification->htmlContent);
		$this->assertSame($channels, $notification->channels);
	}
}
