<?php

declare(strict_types=1);

use App\Message\Notification;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $frameworkConfig): void {
	$frameworkConfig
		->messenger()
		->transport('default')
		->dsn(env('MESSENGER_TRANSPORT_DSN'))
		->retryStrategy()
		->maxRetries(2)
		->delay(1000)
		->multiplier(2)
		->maxDelay(0)
		->jitter(0.2);

	$frameworkConfig
		->messenger()
		->routing(Notification::class)
		->senders(['default']);
};
