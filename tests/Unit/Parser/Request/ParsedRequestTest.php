<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Request;

use App\Parser\ContentInterface;
use App\Parser\Request\ParsedRequest;
use App\Parser\Request\QueryParams;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ParsedRequestTest extends TestCase
{
	public function testParsedRequestIsInitializedCorrectly(): void
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$queryParamsMock = $this->createMock(QueryParams::class);
		$violationsMock = $this->createMock(ConstraintViolationListInterface::class);
		$parsedRequest = new ParsedRequest($contentMock, $queryParamsMock, $violationsMock);
		$this->assertSame($contentMock, $parsedRequest->content);
		$this->assertSame($queryParamsMock, $parsedRequest->queryParams);
		$this->assertSame($violationsMock, $parsedRequest->violations);
	}
}
