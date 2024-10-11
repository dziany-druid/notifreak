<?php

declare(strict_types=1);

namespace App\Message\Formatter\Bugsnag;

use App\Message\ContentInterface;
use Twig\Environment;

final class Content implements ContentInterface
{
	public function __construct(
		private readonly Error $error,
		private readonly Environment $twig,
	) {
	}

	public function plain(): string
	{
		return $this->twig->render('message/bugsnag/plain.txt.twig', ['error' => $this->error]);
	}

	public function html(): string
	{
		return $this->twig->render('message/bugsnag/html.html.twig', ['error' => $this->error]);
	}

	public function markdown(): string
	{
		return $this->twig->render('message/bugsnag/markdown.md.twig', ['error' => $this->error]);
	}
}
