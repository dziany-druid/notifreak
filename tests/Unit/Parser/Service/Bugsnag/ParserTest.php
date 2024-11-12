<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Service\Bugsnag;

use App\Parser\Request\ParsedRequestBuilder;
use App\Parser\Service\Bugsnag\Parser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

final class ParserTest extends TestCase
{
	private MockObject&ValidatorInterface $validator;

	private MockObject&Environment $twig;

	private Parser $parser;

	protected function setUp(): void
	{
		$this->validator = $this->createMock(ValidatorInterface::class);
		$this->twig = $this->createMock(Environment::class);
		$this->parser = new Parser($this->validator, $this->twig);
	}

	public function testSupportsReturnsTrueForBugsnagParser(): void
	{
		$request = new Request();
		$request->attributes->set('parserName', 'bugsnag');
		$result = $this->parser->supports($request);
		$this->assertTrue($result);
	}

	public function testSupportsReturnsFalseForOtherParsers(): void
	{
		$request = new Request();
		$request->attributes->set('parserName', 'other');
		$result = $this->parser->supports($request);
		$this->assertFalse($result);
	}

	public function testParseWithoutValidationErrors(): void
	{
		$body = json_encode([
			'project' => ['
				name' => 'SampleProject',
			],

			'error' => [
				'exceptionClass' => 'SampleException',
				'message' => 'An error occurred',
				'firstReceived' => '1920-05-18T21:37:00.000000Z',
				'receivedAt' => '2005-04-02T21:37:00.000000Z',
				'url' => 'http://example.com',
			],
		]);

		\assert(\is_string($body));

		$request = new Request(
			content: $body,
		);

		$parsedRequestBuilder = $this->createMock(ParsedRequestBuilder::class);
		$this->validator->method('validate')->willReturn(new ConstraintViolationList());

		$parsedRequestBuilder->expects($this->once())
			->method('setContent');

		$this->parser->parse($request, $parsedRequestBuilder);
	}

	public function testParseWithValidationErrors(): void
	{
		$body = json_encode([
			'project' => [
				'name' => '',
			],

			'error' => [
				'exceptionClass' => 'SampleException',
				'message' => 'An error occurred',
			],
		]);

		\assert(\is_string($body));

		$request = new Request(
			content: $body,
		);

		$parsedRequestBuilder = $this->createMock(ParsedRequestBuilder::class);

		$violationList = new ConstraintViolationList([
			new ConstraintViolation(
				message: 'This value should not be blank.',
				messageTemplate: null,
				parameters: [],
				root: null,
				propertyPath: 'project',
				invalidValue: '',
			),
		]);

		$this->validator->method('validate')->willReturn($violationList);

		$parsedRequestBuilder->expects($this->once())
			->method('addViolation')
			->with($violationList);

		$parsedRequestBuilder->expects($this->never())
			->method('setContent');

		$this->parser->parse($request, $parsedRequestBuilder);
	}
}
