<?php declare(strict_types = 1);

namespace Symbiote\SilverstripePHPStan\Tests\Type;

use Symbiote\SilverstripePHPStan\Type\DataListReturnTypeExtension;
use Symbiote\SilverstripePHPStan\Type\DataObjectGetStaticReturnTypeExtension;
use Symbiote\SilverstripePHPStan\ClassHelper;
use PHPStan\Testing\TypeInferenceTestCase;

class DataListReturnTypeExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function dataFileAsserts(): iterable
    {
        // path to a file with actual asserts of expected types:
        yield from $this->gatherAssertTypes(__DIR__ . '/data/data-list-return-types.php');
    }

    /**
     * @dataProvider dataFileAsserts
     */
    public function testFileAsserts(
        string $assertType,
        string $file,
        ...$args
    ): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function getAdditionalConfigFiles(): array
    {
        // path to your project's phpstan.neon, or extension.neon in case of custom extension packages
        return [
            // __DIR__ . '/../extension.neon'
        ];
    }

    // public function dataDynamicMethodReturnTypeExtensions(): array
    // {
    //     return [
    //         // Test `$siteTreeDataList->filter()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->filter(array("ID" => "1"))'),
    //         ],
    //         // Test `$siteTreeDataList->filterAny()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->filterAny(array("ID" => "1"))'),
    //         ],
    //         // Test `$siteTreeDataList->reverse()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->reverse()'),
    //         ],
    //         // Test `SiteTree::get()->where()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->where("1 = 1")'),
    //         ],
    //         // Test `$siteTreeDataList->whereAny()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->whereAny("1 = 1")'),
    //         ],
    //         // Test `$siteTreeDataList->innerJoin()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->innerJoin("1 = 1", "TableName")'),
    //         ],
    //         // Test `$siteTreeDataList->sort()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->sort(array("ID", "Title"))'),
    //         ],
    //         // Test `$siteTreeDataList->limit()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->limit(10)'),
    //         ],
    //         // Test `$siteTreeDataList->exclude()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->exclude(array("ID" => "1"))'),
    //         ],
    //         // Test `$siteTreeDataList->setDataQueryParam()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->setDataQueryParam("Versioned.mode", "all_versions")'),
    //         ],
    //         // Test `$siteTreeDataList->alterDataQuery()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->alterDataQuery(function($query){ $query->reverseSort(); })'),
    //         ],
    //         // Test `$siteTreeDataList->setQueriedColumns()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->setQueriedColumns(array("ID"))'),
    //         ],
    //         // Test `$siteTreeDataList->byIDs()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->byIDs(array(3,5))'),
    //         ],
    //         // Test `$siteTreeDataList->addMany()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->addMany(array(1,2))'),
    //         ],
    //         // Test `$siteTreeDataList->removeMany()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->removeMany(array(1,2))'),
    //         ],
    //         // Test `$siteTreeDataList->removeByFilter()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->removeByFilter(\'"ID" = 1\')'),
    //         ],
    //         // Test `$siteTreeDataList->removeAll()` returns `DataList<SiteTree>`
    //         [
    //             sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
    //             sprintf('$siteTreeDataList->removeAll()'),
    //         ],
    //     ];
    // }
    //
    // /**
    //  * @dataProvider dataDynamicMethodReturnTypeExtensions
    //  * @param string $description
    //  * @param string $expression
    //  */
    // public function testDynamicMethodReturnTypeExtensions(
    //     string $description,
    //     string $expression
    // ) {
    //     $dynamicMethodReturnTypeExtensions = [
    //         new DataListReturnTypeExtension(),
    //     ];
    //     $dynamicStaticMethodReturnTypeExtensions = [
    //         new DataObjectGetStaticReturnTypeExtension(),
    //     ];
    //     $this->assertTypes(
    //         __DIR__ . '/data/data-list-return-types.php',
    //         $description,
    //         $expression,
    //         $dynamicMethodReturnTypeExtensions,
    //         $dynamicStaticMethodReturnTypeExtensions
    //     );
    // }
}
