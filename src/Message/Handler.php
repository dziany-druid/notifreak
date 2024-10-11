<?php

declare(strict_types=1);

namespace App\Message;

use App\Message\Channel\ChannelInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class Handler
{
	/**
	 * @param iterable<ChannelInterface> $supportedChannels
	 */
	public function __construct(
		#[AutowireIterator('app.message.channel')]
		private readonly iterable $supportedChannels,
	) {
	}

	public function __invoke(Notification $message): void
	{
		foreach ($this->supportedChannels as $supportedChannel) {
			if (\in_array($supportedChannel->name(), $message->channels, true)) {
				$supportedChannel->send($message);
			}
		}
	}
}
