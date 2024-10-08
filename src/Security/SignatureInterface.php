<?php

declare(strict_types=1);

namespace App\Security;

interface SignatureInterface
{
	public function generate(string $string): string;

	public function verify(string $signature, string $string): bool;
}
