<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Validator\Channel;
use App\Validator\ChannelValidator;
use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{
	public function testMessageProperty(): void
	{
		$constraint = new Channel();

		$this->assertSame(
			'Channel "{{ channelName }}" is not supported, supported channels: {{ supportedChannelNames }}.',
			$constraint->message,
		);
	}

	public function testValidatedByMethod(): void
	{
		$constraint = new Channel();
		$this->assertSame(ChannelValidator::class, $constraint->validatedBy());
	}
}
