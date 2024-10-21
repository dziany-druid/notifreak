<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser;

use App\Parser\RawContent;
use PHPUnit\Framework\TestCase;

final class RawContentTest extends TestCase
{
	public function testPlainRemovesHtmlTags(): void
	{
		$content = new RawContent('<h1>Hello</h1> <p>World!</p>');
		$this->assertSame('Hello World!', $content->plain());
	}

	public function testHtmlEscapesSpecialCharacters(): void
	{
		$content = new RawContent('<script>alert("XSS")</script>');
		$this->assertSame('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $content->html());
	}

	public function testPlainHandlesEmptyString(): void
	{
		$content = new RawContent('');
		$this->assertSame('', $content->plain());
	}

	public function testHtmlHandlesEmptyString(): void
	{
		$content = new RawContent('');
		$this->assertSame('', $content->html());
	}

	public function testMarkdownHandlesEmptyString(): void
	{
		$content = new RawContent('');
		$this->assertSame('', $content->markdown());
	}
}
