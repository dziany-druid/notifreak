<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
	require __DIR__.'/parameters.php';

	$services = $configurator->services();

	$services
		->defaults()
		->autowire()
		->autoconfigure();

	$services
		->load('App\\', '../src/')
		->exclude('../src/DependencyInjection/')
		->exclude('../src/Entity/')
		->exclude('../src/Kernel.php');

	$services
		->load('App\Controller\\', '../src/Controller/')
		->tag('controller.service_arguments');
};
