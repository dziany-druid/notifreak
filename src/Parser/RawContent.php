<?php

declare(strict_types=1);

namespace App\Parser;

final class RawContent implements ContentInterface
{
	public function __construct(
		private readonly string $requestBody,
	) {
	}

	public function plain(): string
	{
		return strip_tags($this->requestBody);
	}

	public function html(): string
	{
		return htmlspecialchars($this->requestBody);
	}

	public function markdown(): string
	{
		return $this->plain();
	}
}
