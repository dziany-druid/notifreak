<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Request;

use App\Parser\ContentInterface;
use App\Parser\RawContent;
use App\Parser\Request\ParsedRequest;
use App\Parser\Request\ParsedRequestBuilder;
use App\Parser\Request\QueryParams;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

final class ParsedRequestBuilderTest extends TestCase
{
	private ParsedRequestBuilder $builder;

	private MockObject&Request $requestMock;

	protected function setUp(): void
	{
		$this->requestMock = $this->createMock(Request::class);
		$this->requestMock->method('getContent')->willReturn('sample content');
		$this->builder = new ParsedRequestBuilder($this->requestMock);
	}

	public function testInitialValuesAreSetCorrectly(): void
	{
		$built = $this->builder->build();
		$this->assertInstanceOf(RawContent::class, $built->content);
		$this->assertSame('sample content', $built->content->plain());
		$this->assertInstanceOf(QueryParams::class, $built->queryParams);
		$this->assertCount(0, $built->violations);
	}

	public function testSetContent(): void
	{
		$contentMock = $this->createMock(ContentInterface::class);
		$this->builder->setContent($contentMock);
		$this->assertSame($contentMock, $this->builder->build()->content);
	}

	public function testSetQueryParams(): void
	{
		$queryParamsMock = $this->createMock(QueryParams::class);
		$this->builder->setQueryParams($queryParamsMock);
		$this->assertSame($queryParamsMock, $this->builder->build()->queryParams);
	}

	public function testAddSingleViolation(): void
	{
		$violationMock = $this->createMock(ConstraintViolationInterface::class);
		$this->builder->addViolation($violationMock);
		$this->assertCount(1, $this->builder->build()->violations);
	}

	public function testAddMultipleViolations(): void
	{
		$violationListMock = new ConstraintViolationList([
			$this->createMock(ConstraintViolationInterface::class),
			$this->createMock(ConstraintViolationInterface::class),
		]);

		$this->builder->addViolation($violationListMock);
		$this->assertCount(2, $this->builder->build()->violations);
	}

	public function testBuildReturnsParsedRequest(): void
	{
		$parsedRequest = $this->builder->build();
		$this->assertInstanceOf(ParsedRequest::class, $parsedRequest);
		$this->assertSame($this->builder->build()->content, $parsedRequest->content);
		$this->assertSame($this->builder->build()->queryParams, $parsedRequest->queryParams);
		$this->assertSame($this->builder->build()->violations, $parsedRequest->violations);
	}
}
