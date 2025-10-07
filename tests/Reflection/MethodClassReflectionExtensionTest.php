<?php

namespace Syntro\SilverstripePHPStan\Tests\Reflection;

use MethodClassReflectionReturnTypesNamespace\Team;
use PHPStan\Testing\TypeInferenceTestCase;
use SilverStripe\Core\Config\Config;

class MethodClassReflectionExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public static function dataFileAsserts(): iterable
    {
        // path to a file with actual asserts of expected types:
        require_once(__DIR__ . '/data/method-class-reflection.php');
        yield from self::gatherAssertTypes(__DIR__ . '/data/method-class-reflection.php');
    }

    /**
     * @dataProvider dataFileAsserts
     */
    public static function testFileAsserts(
        string $assertType,
        string $file,
        ...$args
    ): void {
        singleton(self::class)->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function getAdditionalConfigFiles(): array
    {
        // path to your project's phpstan.neon, or extension.neon in case of custom extension packages
        return [
            __DIR__ . '/../../phpstan.neon'
        ];
    }
}
