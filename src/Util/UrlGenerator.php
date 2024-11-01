<?php

declare(strict_types=1);

namespace App\Util;

use App\Security\Signature\SignatureInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlGenerator
{
	public function __construct(
		private readonly RouterInterface $router,
		private readonly SignatureInterface $signature,
	) {
	}

	/**
	 * @param string[] $channels
	 */
	public function generate(string $parserName, array $channels): string
	{
		if (empty($channels)) {
			throw new \InvalidArgumentException('Channels must not be empty.');
		}

		$query = [
			'channels' => $channels,
		];

		$url = $this->router->generate(
			'queue_notification',

			[
				'signature' => $this->signature->generate($parserName.serialize($query)),
				'parserName' => $parserName,
			],

			RouterInterface::ABSOLUTE_URL,
		);

		return $url.'?'.http_build_query($query);
	}
}
