<?php

declare(strict_types=1);

namespace App\Message\Formatter;

use App\Message\Formatter\Bugsnag\Parser as BugsnagParser;
use App\Message\Formatter\Raw\Parser as RawParser;
use Twig\Environment;

class ParserFactory
{
	public function __construct(
		private readonly Environment $twig,
	) {
	}

	public function create(string $parser): ParserInterface
	{
		return match ($parser) {
			'bugsnag' => new BugsnagParser($this->twig),
			default => new RawParser(),
		};
	}
}
