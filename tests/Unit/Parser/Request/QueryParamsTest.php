<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parser\Request;

use App\Parser\Request\QueryParams;
use PHPUnit\Framework\TestCase;

final class QueryParamsTest extends TestCase
{
	public function testQueryParamsIsInitializedCorrectly(): void
	{
		$channels = ['channel_one', 'channel_two'];
		$queryParams = new QueryParams($channels);
		$this->assertSame($channels, $queryParams->channels);
	}
}
