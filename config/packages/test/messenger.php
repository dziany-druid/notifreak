<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $frameworkConfig): void {
	$frameworkConfig
		->messenger()
		->transport('default')
		->dsn('in-memory://');
};
