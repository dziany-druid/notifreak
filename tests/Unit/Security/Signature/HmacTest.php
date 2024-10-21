<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Signature;

use App\Security\Signature\Hmac;
use PHPUnit\Framework\TestCase;

final class HmacTest extends TestCase
{
	private string $securityKey;

	protected function setUp(): void
	{
		$this->securityKey = 'secret';
	}

	public function testGenerate(): void
	{
		$hmacSignature = new Hmac($this->securityKey);
		$generatedSignature = $hmacSignature->generate('test');
		$this->assertSame('0329a06b62cd16b33eb6792be8c60b158d89a2ee3a876fce9a881ebb488c0914', $generatedSignature);
	}

	public function testVerifyReturnsTrueForValidSignature(): void
	{
		$hmacSignature = new Hmac($this->securityKey);
		$inputString = 'test';
		$signature = $hmacSignature->generate($inputString);
		$this->assertTrue($hmacSignature->verify($signature, $inputString));
	}

	public function testVerifyReturnsFalseForInvalidSignature(): void
	{
		$hmacSignature = new Hmac($this->securityKey);
		$signature = $hmacSignature->generate('test');
		$this->assertFalse($hmacSignature->verify($signature, 'invalid'));
	}
}
