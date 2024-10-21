<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $frameworkConfig): void {
	$frameworkConfig->secret(env('APP_SECRET'));

	$frameworkConfig
		->session()
		->enabled(false);
};
