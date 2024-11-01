<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util\Command;

use App\Command\GenerateUrlCommand;
use App\Util\UrlGenerator;
use App\Validator\ChannelValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class GenerateUrlCommandTest extends TestCase
{
	private MockObject&ChannelValidator $channelValidator;

	private MockObject&UrlGenerator $urlGenerator;

	private CommandTester $commandTester;

	protected function setUp(): void
	{
		$this->channelValidator = $this->createMock(ChannelValidator::class);
		$this->urlGenerator = $this->createMock(UrlGenerator::class);
		$command = new GenerateUrlCommand($this->channelValidator, $this->urlGenerator);
		$this->commandTester = new CommandTester($command);
	}

	public function testExecuteSuccessfully(): void
	{
		$parserName = 'bugsnag';
		$channels = ['channel_one', 'channel_two'];
		$generatedUrl = 'http://example.com/notifcation/signature/bugsnag?channels[]=channel_one&channels[]=channel_two';

		$this->channelValidator
			->expects($this->once())
			->method('supportedChannelNames')
			->willReturn($channels);

		$this->urlGenerator
			->expects($this->once())
			->method('generate')
			->with($parserName, $channels)
			->willReturn($generatedUrl);

		$this->commandTester->setInputs([$parserName, implode(',', $channels)]);
		$this->commandTester->execute([]);
		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString($generatedUrl, $output);
		$this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
	}
}
