<?php

declare(strict_types=1);

namespace App\Parser\Request;

use App\Parser\ContentInterface;
use App\Parser\RawContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ParsedRequestBuilder
{
	private ContentInterface $content;

	private QueryParams $queryParams;

	private ConstraintViolationListInterface $violations;

	public function __construct(
		private readonly Request $request,
	) {
		$this->violations = new ConstraintViolationList();
		$this->content = new RawContent($this->request->getContent());
		$this->queryParams = new QueryParams([]);
	}

	public function setContent(ContentInterface $content): void
	{
		$this->content = $content;
	}

	public function setQueryParams(QueryParams $queryParams): void
	{
		$this->queryParams = $queryParams;
	}

	public function addViolation(ConstraintViolationInterface|ConstraintViolationListInterface $violation): void
	{
		if ($violation instanceof ConstraintViolationListInterface) {
			$this->violations->addAll($violation);
		} else {
			$this->violations->add($violation);
		}
	}

	public function build(): ParsedRequest
	{
		return new ParsedRequest(
			$this->content,
			$this->queryParams,
			$this->violations,
		);
	}
}
