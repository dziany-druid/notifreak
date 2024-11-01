<?php

declare(strict_types=1);

namespace App\Message;

use App\Parser\ContentInterface;

readonly class Notification
{
	public string $plainContent;

	public string $markdownContent;

	public string $htmlContent;

	/**
	 * @param string[] $channels
	 */
	public function __construct(
		ContentInterface $content,
		public array $channels,
	) {
		$this->plainContent = $content->plain();
		$this->markdownContent = $content->markdown();
		$this->htmlContent = $content->html();
	}
}
