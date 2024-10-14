<?php

declare(strict_types=1);

namespace App\Message;

use App\Parser\ContentInterface;

readonly class Notification
{
	/**
	 * @param string[] $channels
	 */
	public function __construct(
		public ContentInterface $content,
		public array $channels,
	) {
	}
}
