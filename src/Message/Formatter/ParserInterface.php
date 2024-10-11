<?php

declare(strict_types=1);

namespace App\Message\Formatter;

use App\Message\ContentInterface;

interface ParserInterface
{
	public function parse(string $requestBody): ContentInterface;
}
