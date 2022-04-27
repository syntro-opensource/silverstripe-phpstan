<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Type;

use Syntro\SilverstripePHPStan\Type\DataListReturnTypeExtension;
use Syntro\SilverstripePHPStan\Type\DataObjectGetStaticReturnTypeExtension;
use Syntro\SilverstripePHPStan\ClassHelper;
use PHPStan\Testing\TypeInferenceTestCase;

class DataListReturnTypeExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function dataFileAsserts(): iterable
    {
        // path to a file with actual asserts of expected types:
        require_once(__DIR__ . '/data/data-list-return-types.php');
        yield from $this->gatherAssertTypes(__DIR__ . '/data/data-list-return-types.php');
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
