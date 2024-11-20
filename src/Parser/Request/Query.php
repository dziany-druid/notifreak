<?php

declare(strict_types=1);

namespace App\Parser\Request;

use App\Parser\ParserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Query implements ParserInterface
{
	public function __construct(
		private readonly ValidatorInterface $validator,
	) {
	}

	public function supports(Request $request): bool
	{
		return true;
	}

	public function parse(Request $request, ParsedRequestBuilder $parsedRequestBuilder): void
	{
		/** @var string[] $channels */
		$channels = $request->query->all('channels');

		$queryParams = new QueryParams($channels);
		$parsedRequestBuilder->addViolation($this->validator->validate($queryParams));
		$parsedRequestBuilder->setQueryParams($queryParams);
	}
}
