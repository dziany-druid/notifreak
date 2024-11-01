<?php

declare(strict_types=1);

namespace App\Command;

use App\Util\UrlGenerator;
use App\Validator\ChannelValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:generate-url', description: 'Generates a URL to which services can send a request')]
class GenerateUrlCommand extends Command
{
	public function __construct(
		private readonly ChannelValidator $channelValidator,
		private readonly UrlGenerator $urlGenerator,
		?string $name = null,
	) {
		parent::__construct($name);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$parserQuestion = new Question(
			'Please specify which request parser to use (e.g., "bugsnag")',
			'raw',
		);

		$channelsQuestion = new ChoiceQuestion(
			'Please select the channels to which notification should be sent (you can choose multiple channels, separated by commas)',
			$this->channelValidator->supportedChannelNames(),
		);

		$channelsQuestion->setMultiselect(true);
		$io = new SymfonyStyle($input, $output);

		/** @var string $parserName */
		$parserName = $io->askQuestion($parserQuestion);

		/** @var string[] $channels */
		$channels = $io->askQuestion($channelsQuestion);

		$url = $this->urlGenerator->generate($parserName, $channels);
		$io->writeln('<href='.$url.'>'.$url.'</>');
		$io->newLine();

		return Command::SUCCESS;
	}
}
