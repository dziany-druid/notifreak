<?php

declare(strict_types=1);

namespace App;

use App\Message\Channel\ChannelInterface;
use App\Parser\ParserInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
	use MicroKernelTrait;

	protected function build(ContainerBuilder $container): void
	{
		parent::build($container);

		$container
			->registerForAutoconfiguration(ChannelInterface::class)
			->addTag('app.message.channel');

		$container
			->registerForAutoconfiguration(ParserInterface::class)
			->addTag('app.request.parser');
	}
}
