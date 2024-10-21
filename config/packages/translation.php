<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $framework): void {
	$framework->defaultLocale(env('APP_LOCALE'));

	$framework
		->translator()
		->defaultPath('%kernel.project_dir%/translations');
};
