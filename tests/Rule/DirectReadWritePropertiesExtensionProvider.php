<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Rule;

use PHPStan\Rules\Properties\ReadWritePropertiesExtensionProvider;

class DirectReadWritePropertiesExtensionProvider implements ReadWritePropertiesExtensionProvider /* @phpstan-ignore-line */
{

    /**
     * @var ReadWritePropertiesExtension[]
     */
    private $extensions;

    /**
     * @param ReadWritePropertiesExtension[] $extensions
     */
    public function __construct( $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @return ReadWritePropertiesExtension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

}
