<?php

declare(strict_types=1);

namespace App\Parser\Request;

use App\Validator\Channel;
use Symfony\Component\Validator\Constraints as Assert;

readonly class QueryParams
{
	/**
	 * @param string[] $channels
	 */
	public function __construct(
		#[Assert\NotBlank]
		#[Assert\All([
			new Assert\NotBlank(),
			new Channel(),
		])]
		public array $channels,
	) {
	}
}
