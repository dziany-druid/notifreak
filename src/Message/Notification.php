<?php

declare(strict_types=1);

namespace App\Message;

use App\Validator\Channel;
use Symfony\Component\Validator\Constraints as Assert;

readonly class Notification
{
	/**
	 * @param string[] $channels
	 */
	public function __construct(
		public ContentInterface $content,

		#[Assert\NotBlank]
		#[Assert\All([
			new Assert\NotBlank(),
			new Channel(),
		])]
		public array $channels,
	) {
	}
}
