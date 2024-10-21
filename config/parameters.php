<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/* @var ContainerConfigurator $configurator */
$configurator
	->parameters()
	->set('app.timezone', env('APP_TIMEZONE'))
	->set('app.security_key', env('SECURITY_KEY'));
