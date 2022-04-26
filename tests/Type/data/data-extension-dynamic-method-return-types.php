<?php
// @codingStandardsIgnoreStart
namespace DataExtensionDynamicMethodReturnTypesNamespace;

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
            \DataExtensionDynamicMethodReturnTypesNamespace\Foo::class,
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
