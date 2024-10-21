<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $frameworkConfig): void {
	$frameworkConfig
		->notifier()
		->messageBus(false)
		->chatterTransport('telegram', env('TELEGRAM_DSN'));
};
