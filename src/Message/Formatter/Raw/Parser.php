<?php

declare(strict_types=1);

namespace App\Message\Formatter\Raw;

use App\Message\ContentInterface;
use App\Message\Formatter\ParserInterface;

final class Parser implements ParserInterface
{
	public function parse(string $requestBody): ContentInterface
	{
		return new Content($requestBody);
	}
}
