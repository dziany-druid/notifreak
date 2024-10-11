<?php

declare(strict_types=1);

namespace App\Message\Channel;

use App\Message\Notification;

interface ChannelInterface
{
	public function name(): string;

	public function send(Notification $message): void;
}
