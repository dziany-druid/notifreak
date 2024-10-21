<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Request;

use App\Parser\Request\ParsedRequestBuilder;
use App\Parser\Request\Query;
use App\Parser\Request\QueryParams;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class QueryTest extends TestCase
{
	private Query $query;

	private MockObject&ValidatorInterface $validatorMock;

	private MockObject&ParsedRequestBuilder $parsedRequestBuilderMock;

	protected function setUp(): void
	{
		$this->validatorMock = $this->createMock(ValidatorInterface::class);
		$this->parsedRequestBuilderMock = $this->createMock(ParsedRequestBuilder::class);
		$this->query = new Query($this->validatorMock);
	}

	public function testSupportsReturnsTrue(): void
	{
		$requestMock = $this->createMock(Request::class);
		$this->assertTrue($this->query->supports($requestMock));
	}

	public function testParseValidChannels(): void
	{
		$channels = ['channel_one', 'channel_two'];
		$requestMock = new Request(['channels' => $channels]);

		$this->validatorMock
			->method('validate')
			->willReturn(new ConstraintViolationList());

		$this->parsedRequestBuilderMock
			->expects($this->once())
			->method('setQueryParams')
			->with($this->callback(static function (QueryParams $queryParams) use ($channels) {
				return $queryParams->channels === $channels;
			}));

		$this->parsedRequestBuilderMock
			->expects($this->once())
			->method('addViolation')
			->with($this->anything());

		$this->query->parse($requestMock, $this->parsedRequestBuilderMock);
	}

	public function testParseInvalidChannels(): void
	{
		$channels = ['channel_one', ''];
		$requestMock = new Request(['channels' => $channels]);

		$violationList = new ConstraintViolationList([
			new ConstraintViolation(
				message: 'This value should not be blank.',
				messageTemplate: null,
				parameters: [],
				root: null,
				propertyPath: 'channels[1]',
				invalidValue: $channels[1],
			),
		]);

		$this->validatorMock
			->method('validate')
			->willReturn($violationList);

		$this->parsedRequestBuilderMock
			->expects($this->once())
			->method('setQueryParams')
			->with($this->callback(static function (QueryParams $queryParams) use ($channels) {
				return $queryParams->channels === $channels;
			}));

		$this->parsedRequestBuilderMock
			->expects($this->once())
			->method('addViolation')
			->with($violationList);

		$this->query->parse($requestMock, $this->parsedRequestBuilderMock);
	}
}
