<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Service\Bugsnag;

use App\Parser\Service\Bugsnag\Error;
use App\Parser\Service\Bugsnag\ErrorCreator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ErrorCreatorTest extends TestCase
{
	private MockObject&ValidatorInterface $validator;

	protected function setUp(): void
	{
		$this->validator = $this->createMock(ValidatorInterface::class);
	}

	public function testCreateErrorSuccessfully(): void
	{
		$dateFormat = 'Y-m-d\TH:i:s.u\Z';
		$project = 'SampleProject';
		$exceptionClass = 'SampleException';
		$message = 'An error occurred';
		$firstReceived = '1920-05-18T21:37:00.000000Z';
		$receivedAt = '2005-04-02T21:37:00.000000Z';
		$url = 'http://example.com/error';

		$this->validator
			->method('validate')
			->willReturn(new ConstraintViolationList());

		$errorCreator = new ErrorCreator(
			$project,
			$exceptionClass,
			$message,
			$firstReceived,
			$receivedAt,
			$url,
			$this->validator,
		);

		$error = $errorCreator->create();
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame($project, $error->project);
		$this->assertSame($exceptionClass, $error->exceptionClass);
		$this->assertSame($message, $error->message);
		$this->assertSame($firstReceived, $error->firstReceived->format($dateFormat));
		$this->assertSame($receivedAt, $error->receivedAt->format($dateFormat));
		$this->assertSame($url, $error->url);
	}

	public function testCreateErrorWithValidationErrors(): void
	{
		$project = '';
		$exceptionClass = 'SampleException';
		$message = 'An error occurred';
		$firstReceived = '1920-05-18T21:37:00.000000Z';
		$receivedAt = '2005-04-02T21:37:00.000000Z';
		$url = 'not a url';

		$violationList = new ConstraintViolationList([
			new ConstraintViolation('This value should not be blank.', null, [], '', 'project', ''),
			new ConstraintViolation('This value is not a valid URL.', null, [], '', 'url', ''),
		]);

		$this->validator
			->method('validate')
			->willReturn($violationList);

		$errorCreator = new ErrorCreator(
			$project,
			$exceptionClass,
			$message,
			$firstReceived,
			$receivedAt,
			$url,
			$this->validator,
		);

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessageMatches('/project|url/');
		$errorCreator->create();
	}
}
