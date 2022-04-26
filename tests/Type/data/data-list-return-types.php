<?php

namespace DataListDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;
use Symbiote\SilverstripePHPStan\ClassHelper;
use function PHPStan\Testing\assertNativeType;

class Foo
{
    public function doFoo()
    {
        $siteTreeDataList = SiteTree::get();
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->filter(array("ID" => "1"))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->filterAny(array("ID" => "1"))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->reverse()
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->where("1 = 1")
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->whereAny("1 = 1")
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->innerJoin("1 = 1", "TableName")
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->sort(array("ID", "Title"))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->limit(10)
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->exclude(array("ID" => "1"))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->setDataQueryParam("Versioned.mode", "all_versions")
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->alterDataQuery(function($query){ $query->reverseSort(); })
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->setQueriedColumns(array("ID"))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->byIDs(array(3,5))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->addMany(array(1,2))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->removeMany(array(1,2))
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->removeByFilter('"ID" = 1')
        );
        assertNativeType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->removeAll()
        );
        die;
    }
}
