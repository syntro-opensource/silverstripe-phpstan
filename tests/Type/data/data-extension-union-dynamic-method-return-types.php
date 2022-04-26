<?php
// @codingStandardsIgnoreStart
namespace DataExtensionUnionDynamicMethodReturnTypesNamespace;

// SilverStripe
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataExtension;
use function PHPStan\Testing\assertType;

class FooDataExtension extends DataExtension
{
    public function doFoo()
    {
        $owner = $this->getOwner();
        assertType(
            sprintf(
                '%s|%s',
                \DataExtensionUnionDynamicMethodReturnTypesNamespace\Foo::class,
                \DataExtensionUnionDynamicMethodReturnTypesNamespace\FooTwo::class
            ),
            $this->getOwner()
        );
        die;
    }
}

class Foo extends DataObject
{
}

class FooTwo extends DataObject
{
}
// @codingStandardsIgnoreEnd
