<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Service\Bugsnag;

use App\Parser\Service\Bugsnag\Error;
use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
	public function testErrorIsInitializedCorrectly(): void
	{
		$project = 'SampleProject';
		$exceptionClass = 'SampleException';
		$message = 'An error occurred';
		$firstReceived = new \DateTimeImmutable('1920-05-18 21:37:00');
		$receivedAt = new \DateTimeImmutable('2005-04-02 21:37:00');
		$url = 'http://example.com/error';

		$error = new Error(
			project: $project,
			exceptionClass: $exceptionClass,
			message: $message,
			firstReceived: $firstReceived,
			receivedAt: $receivedAt,
			url: $url,
		);

		$this->assertSame($project, $error->project);
		$this->assertSame($exceptionClass, $error->exceptionClass);
		$this->assertSame($message, $error->message);
		$this->assertSame($firstReceived, $error->firstReceived);
		$this->assertSame($receivedAt, $error->receivedAt);
		$this->assertSame($url, $error->url);
	}
}
