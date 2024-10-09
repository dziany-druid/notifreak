<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Channel extends Constraint
{
	public string $message = 'Channel "{{ channelName }}" is not supported, supported channels: {{ supportedChannelNames }}.';

	public function validatedBy(): string
	{
		return ChannelValidator::class;
	}
}
