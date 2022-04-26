<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Tests\Type;

use Symbiote\SilverstripePHPStan\Type\DataListReturnTypeExtension;
use Symbiote\SilverstripePHPStan\Type\DataObjectGetStaticReturnTypeExtension;
use Symbiote\SilverstripePHPStan\ClassHelper;
use Symbiote\SilverstripePHPStan\ConfigHelper;
use PHPStan\Testing\TypeInferenceTestCase;

class ExtensionReturnTypeExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function dataFileAsserts(): iterable
    {
        // path to a file with actual asserts of expected types:
        require_once(__DIR__ . '/data/data-extension-dynamic-method-return-types.php');
        require_once(__DIR__ . '/data/data-extension-union-dynamic-method-return-types.php');

        ConfigHelper::update(
            \DataExtensionDynamicMethodReturnTypesNamespace\Foo::class,
            'extensions',
            [
            \DataExtensionDynamicMethodReturnTypesNamespace\FooDataExtension::class,
            ]
        );
        // yield from $this->gatherAssertTypes(__DIR__ . '/data/data-extension-dynamic-method-return-types.php');


        $extensions = [
            \DataExtensionUnionDynamicMethodReturnTypesNamespace\FooDataExtension::class,
        ];
        ConfigHelper::update(
            \DataExtensionUnionDynamicMethodReturnTypesNamespace\Foo::class,
            'extensions',
            $extensions
        );
        ConfigHelper::update(
            \DataExtensionUnionDynamicMethodReturnTypesNamespace\FooTwo::class,
            'extensions',
            $extensions
        );

        yield from $this->gatherAssertTypes(__DIR__ . '/data/data-extension-dynamic-method-return-types.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/data-extension-union-dynamic-method-return-types.php');
    }

    /**
     * @dataProvider dataFileAsserts
     */
    public function testFileAsserts(
        string $assertType,
        string $file,
        ...$args
    ): void {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function getAdditionalConfigFiles(): array
    {
        // path to your project's phpstan.neon, or extension.neon in case of custom extension packages
        return [
            __DIR__ . '/../../phpstan.neon'
        ];
    }
}
