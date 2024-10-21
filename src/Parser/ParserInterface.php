<?php

declare(strict_types=1);

namespace App\Parser;

use App\Parser\Request\ParsedRequestBuilder;
use Symfony\Component\HttpFoundation\Request;

interface ParserInterface
{
	public function supports(Request $request): bool;

	public function parse(Request $request, ParsedRequestBuilder $parsedRequestBuilder): void;
}
