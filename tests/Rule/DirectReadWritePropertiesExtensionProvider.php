<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Rule;

class DirectReadWritePropertiesExtensionProvider implements ReadWritePropertiesExtensionProvider
{

    /**
     * @var array
     */
    private $extensions

	/**
	 * @param ReadWritePropertiesExtension[] $extensions
	 */
	public function __construct(array $extensions)
	{
        $this->extensions = $extensions;
	}

	/**
	 * @return ReadWritePropertiesExtension[]
	 */
	public function getExtensions(): array
	{
		return $this->extensions;
	}

}
