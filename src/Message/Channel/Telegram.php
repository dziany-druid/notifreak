<?php

declare(strict_types=1);

namespace App\Message\Channel;

use App\Message\Notification;

final class Telegram implements ChannelInterface
{
	public function name(): string
	{
		return 'telegram';
	}

	public function send(Notification $notification): void
	{
	}
}
