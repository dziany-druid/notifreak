<?php

declare(strict_types=1);

namespace App\Validator;

use App\Message\Channel\ChannelInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChannelValidator extends ConstraintValidator
{
	/**
	 * @param iterable<ChannelInterface> $channels
	 */
	public function __construct(
		private readonly TranslatorInterface $translator,

		#[AutowireIterator('app.message.channel')]
		private readonly iterable $channels,
	) {
	}

	/**
	 * @param Channel $constraint
	 */
	public function validate(mixed $value, Constraint $constraint): void
	{
		$supportedChannelNames = $this->supportedChannelNames();

		if (!\in_array($value, $supportedChannelNames, true)) {
			$this->context
				->buildViolation($this->translator->trans($constraint->message))
				->setParameter('{{ channelName }}', \is_string($value) ? $value : serialize($value))
				->setParameter('{{ supportedChannelNames }}', '"'.implode('", "', $supportedChannelNames).'"')
				->addViolation();
		}
	}

	/**
	 * @return string[]
	 */
	public function supportedChannelNames(): array
	{
		$supportedChannelNames = [];

		foreach ($this->channels as $channel) {
			$supportedChannelNames[] = $channel->name();
		}

		return $supportedChannelNames;
	}
}
