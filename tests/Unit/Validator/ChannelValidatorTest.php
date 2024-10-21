<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Message\Channel\ChannelInterface;
use App\Validator\Channel;
use App\Validator\ChannelValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<ChannelValidator>
 */
final class ChannelValidatorTest extends ConstraintValidatorTestCase
{
	public function testValidChannel(): void
	{
		$this->validator->validate('one', new Channel());
		$this->assertNoViolation();
	}

	public function testInvalidChannel(): void
	{
		$channel = new Channel();
		$this->validator->validate('three', new Channel());

		$this->buildViolation($channel->message)
			->setParameter('{{ channelName }}', 'three')
			->setParameter('{{ supportedChannelNames }}', '"one", "two"')
			->assertRaised();
	}

	public function testNonStringInvalidChannel(): void
	{
		$channel = new Channel();
		$this->validator->validate(['array'], new Channel());

		$this->buildViolation($channel->message)
			->setParameter('{{ channelName }}', 'a:1:{i:0;s:5:"array";}')
			->setParameter('{{ supportedChannelNames }}', '"one", "two"')
			->assertRaised();
	}

	protected function createValidator(): ConstraintValidatorInterface
	{
		$channelOneMock = $this->createMock(ChannelInterface::class);
		$channelOneMock->method('name')->willReturn('one');
		$channelTwoMock = $this->createMock(ChannelInterface::class);
		$channelTwoMock->method('name')->willReturn('two');

		$channels = [
			$channelOneMock,
			$channelTwoMock,
		];

		return new ChannelValidator($channels);
	}
}
