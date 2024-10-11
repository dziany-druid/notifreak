<?php

declare(strict_types=1);

namespace App\Message\Formatter\Bugsnag;

use App\Message\ContentInterface;
use App\Message\Formatter\ParserInterface;
use Twig\Environment;

final class Parser implements ParserInterface
{
	public function __construct(
		private readonly Environment $twig,
	) {
	}

	public function parse(string $requestBody): ContentInterface
	{
		$errorArray = json_decode(
			json: $requestBody,
			associative: true,
			flags: \JSON_THROW_ON_ERROR,
		);

		if (!\is_array($errorArray) || empty($errorArray)) {
			throw new \JsonException('Unable to parse request body');
		}

		$error = new Error(
			$errorArray['project']['name'],
			$errorArray['error']['exceptionClass'],
			$errorArray['error']['message'],
			new \DateTimeImmutable($errorArray['error']['firstReceived']),
			new \DateTimeImmutable($errorArray['error']['receivedAt']),
			$errorArray['error']['url'],
		);

		return new Content($error, $this->twig);
	}
}
