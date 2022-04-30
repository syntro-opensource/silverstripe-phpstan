<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Rule;

class DirectReadWritePropertiesExtensionProvider implements ReadWritePropertiesExtensionProvider
{

	/**
	 * @param ReadWritePropertiesExtension[] $extensions
	 */
	public function __construct(private array $extensions)
	{
	}

	/**
	 * @return ReadWritePropertiesExtension[]
	 */
	public function getExtensions(): array
	{
		return $this->extensions;
	}

}
