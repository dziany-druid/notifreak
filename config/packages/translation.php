<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $frameworkConfig): void {
	$frameworkConfig->defaultLocale(env('APP_LOCALE'));

	$frameworkConfig
		->translator()
		->defaultPath('%kernel.project_dir%/translations');
};
