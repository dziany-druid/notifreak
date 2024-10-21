<?php

declare(strict_types=1);

namespace App\Parser\Service\Bugsnag;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ErrorCreator
{
	public readonly ConstraintViolationListInterface $violations;

	private const VALID_DATE_FORMAT = 'Y-m-d\TH:i:s.u\Z';

	/**
	 * @param string $project
	 * @param string $exceptionClass
	 * @param string $message
	 * @param string $firstReceived
	 * @param string $receivedAt
	 * @param string $url
	 */
	public function __construct(
		#[Assert\NotBlank]
		#[Assert\Type('string')]
		private readonly mixed $project,

		#[Assert\NotBlank]
		#[Assert\Type('string')]
		private readonly mixed $exceptionClass,

		#[Assert\NotBlank]
		#[Assert\Type('string')]
		private readonly mixed $message,

		#[Assert\NotBlank]
		#[Assert\DateTime(format: self::VALID_DATE_FORMAT)]
		private readonly mixed $firstReceived,

		#[Assert\NotBlank]
		#[Assert\DateTime(format: self::VALID_DATE_FORMAT)]
		private readonly mixed $receivedAt,

		#[Assert\NotBlank]
		#[Assert\Type('string')]
		#[Assert\Url]
		private readonly mixed $url,

		ValidatorInterface $validator,
	) {
		$this->violations = $validator->validate($this);
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function create(): Error
	{
		if (\count($this->violations) > 0) {
			throw new \InvalidArgumentException((string) $this->violations);
		}

		return new Error(
			$this->project,
			$this->exceptionClass,
			$this->message,
			new \DateTimeImmutable($this->firstReceived),
			new \DateTimeImmutable($this->receivedAt),
			$this->url,
		);
	}
}
