<?php

declare(strict_types=1);

namespace App\Security\Signature;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class Hmac implements SignatureInterface
{
	private readonly string $securityKey;

	public function __construct(
		#[\SensitiveParameter]
		#[Autowire(param: 'app.security_key')]
		string $securityKey,
	) {
		$this->securityKey = $securityKey;
	}

	public function verify(string $signature, string $string): bool
	{
		return hash_equals($this->generate($string), $signature);
	}

	public function generate(string $string): string
	{
		return hash_hmac('sha256', $string, $this->securityKey);
	}
}
