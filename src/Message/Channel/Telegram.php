<?php

declare(strict_types=1);

namespace App\Message\Channel;

use App\Message\Notification;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final class Telegram implements ChannelInterface
{
	public function __construct(
		private readonly ChatterInterface $chatter,
	) {
	}

	public function name(): string
	{
		return 'telegram';
	}

	public function send(Notification $notification): void
	{
		$options = (new TelegramOptions())
			->parseMode(TelegramOptions::PARSE_MODE_MARKDOWN)
			->disableWebPagePreview(true);

		$chatMessage = new ChatMessage(
			$notification->content->markdown(),
			$options,
		);

		$chatMessage->transport($this->name());
		$this->chatter->send($chatMessage);
	}
}
