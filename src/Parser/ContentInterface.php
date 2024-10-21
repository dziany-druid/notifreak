<?php

declare(strict_types=1);

namespace App\Parser;

interface ContentInterface
{
	public function plain(): string;

	public function html(): string;

	public function markdown(): string;
}
