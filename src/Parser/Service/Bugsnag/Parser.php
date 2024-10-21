<?php

declare(strict_types=1);

namespace App\Parser\Service\Bugsnag;

use App\Parser\ParserInterface;
use App\Parser\Request\ParsedRequestBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

final class Parser implements ParserInterface
{
	public function __construct(
		private readonly ValidatorInterface $validator,
		private readonly Environment $twig,
	) {
	}

	public function supports(Request $request): bool
	{
		return 'bugsnag' === $request->attributes->get('parserName');
	}

	public function parse(Request $request, ParsedRequestBuilder $parsedRequestBuilder): void
	{
		$body = $request->getContent();
		$errorArray = [];

		if (json_validate($body)) {
			/**
			 * @var array{
			 *     project?: array{
			 *         name?: string
			 *     },
			 *     error?: array{
			 *         exceptionClass?: string,
			 *         message?: string,
			 *         firstReceived?: string,
			 *         receivedAt?: string,
			 *         url?: string
			 *     }
			 * } $errorArray
			 */
			$errorArray = json_decode(
				json: $body,
				associative: true,
				flags: \JSON_THROW_ON_ERROR,
			);
		}

		$errorCreator = new ErrorCreator(
			$errorArray['project']['name'] ?? '',
			$errorArray['error']['exceptionClass'] ?? '',
			$errorArray['error']['message'] ?? '',
			$errorArray['error']['firstReceived'] ?? '',
			$errorArray['error']['receivedAt'] ?? '',
			$errorArray['error']['url'] ?? '',
			$this->validator,
		);

		if (\count($errorCreator->violations) > 0) {
			$parsedRequestBuilder->addViolation($errorCreator->violations);

			return;
		}

		$parsedRequestBuilder->setContent(new Content($errorCreator->create(), $this->twig));
	}
}
