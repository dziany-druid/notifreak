<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
	->in(__DIR__)
	->ignoreDotFiles(true)
	->ignoreUnreadableDirs(true)
	->ignoreVCSIgnored(true);

return (new PhpCsFixer\Config())
	->setFinder($finder)
	->setIndent('	')
	->setLineEnding("\n")
	->setRiskyAllowed(true)
	->setRules([
		'@PSR12' => true,
		'@PSR12:risky' => true,
		'@Symfony' => true,
		'@Symfony:risky' => true,
		'class_keyword' => true,
		'mb_str_functions' => true,
		'date_time_create_from_format_call' => true,
		'declare_strict_types' => true,
		'void_return' => true,
		'static_lambda' => true,

		'blank_line_before_statement' => [
			'statements' => [
				'case',
				'default',
				'do',
				'for',
				'foreach',
				'if',
				'switch',
				'try',
				'while',
				'phpdoc',
				'return',
			],
		],

		'ordered_class_elements' => [
			'order' => [
				'use_trait',
				'constant_public',
				'property_public_readonly',
				'property_public',
				'property_public_static',
				'constant_protected',
				'property_protected_readonly',
				'property_protected',
				'property_protected_static',
				'constant_private',
				'property_private_readonly',
				'property_private',
				'property_private_static',
				'construct',
				'magic',
				'phpunit',
				'method_public',
				'method_public_static',
				'method_public_abstract',
				'method_public_abstract_static',
				'method_protected',
				'method_protected_static',
				'method_protected_abstract',
				'method_protected_abstract_static',
				'method_private',
				'method_private_static',
				'destruct',
			],
		],
	]);
