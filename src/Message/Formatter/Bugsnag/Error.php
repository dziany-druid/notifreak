<?php

declare(strict_types=1);

namespace App\Message\Formatter\Bugsnag;

final readonly class Error
{
	public function __construct(
		public string $project,
		public string $exceptionClass,
		public string $message,
		public \DateTimeImmutable $firstReceived,
		public \DateTimeImmutable $receivedAt,
		public string $url,
	) {
	}
}
