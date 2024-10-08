<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Security\HmacSignature;
use PHPUnit\Framework\TestCase;

class HmacSignatureTest extends TestCase
{
	private string $securityKey;

	protected function setUp(): void
	{
		$this->securityKey = 'secret';
	}

	public function testGenerate(): void
	{
		$hmacSignature = new HmacSignature($this->securityKey);
		$generatedSignature = $hmacSignature->generate('test');
		$this->assertSame('0329a06b62cd16b33eb6792be8c60b158d89a2ee3a876fce9a881ebb488c0914', $generatedSignature);
	}

	public function testVerifyReturnsTrueForValidSignature(): void
	{
		$hmacSignature = new HmacSignature($this->securityKey);
		$inputString = 'test';
		$signature = $hmacSignature->generate($inputString);
		$this->assertTrue($hmacSignature->verify($signature, $inputString));
	}

	public function testVerifyReturnsFalseForInvalidSignature(): void
	{
		$hmacSignature = new HmacSignature($this->securityKey);
		$signature = $hmacSignature->generate('test');
		$this->assertFalse($hmacSignature->verify($signature, 'invalid'));
	}
}
