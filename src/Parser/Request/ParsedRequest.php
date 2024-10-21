<?php

declare(strict_types=1);

namespace App\Parser\Request;

use App\Parser\ContentInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

readonly class ParsedRequest
{
	public function __construct(
		public ContentInterface $content,
		public QueryParams $queryParams,
		public ConstraintViolationListInterface $violations,
	) {
	}
}
