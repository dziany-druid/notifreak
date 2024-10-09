<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Channel;

use App\Message\Channel\Telegram;
use PHPUnit\Framework\TestCase;

class TelegramTest extends TestCase
{
	public function testNameReturnsCorrectChannelName(): void
	{
		$telegram = new Telegram();
		$this->assertSame('telegram', $telegram->name());
	}
}
