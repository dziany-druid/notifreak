<?php

declare(strict_types=1);

namespace App\Message\Channel;

use App\Message\Notification;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final class Telegram implements ChannelInterface
{
	private const MAX_MESSAGE_SIZE = 4096;

	private const ENCODING = 'UTF-8';

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
		// Telegram message must not be empty.
		if ('' === $notification->content->plain()) {
			return;
		}

		$options = (new TelegramOptions())
			->disableWebPagePreview(true);

		$content = $notification->content->markdown();
		$chunks = [];

		if (mb_strlen($content, self::ENCODING) > self::MAX_MESSAGE_SIZE) {
			$chunks = mb_str_split($content, self::MAX_MESSAGE_SIZE);
		} else {
			$chunks[] = $content;
			$options->parseMode(TelegramOptions::PARSE_MODE_MARKDOWN);
		}

		foreach ($chunks as $chunk) {
			$chatMessage = new ChatMessage(
				$chunk,
				$options,
			);

			$chatMessage->transport($this->name());
			$this->chatter->send($chatMessage);
		}
	}
}
