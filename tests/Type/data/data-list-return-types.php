<?php

namespace DataListDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;
use Syntro\SilverstripePHPStan\ClassHelper;
use function PHPStan\Testing\assertType;

class Foo
{
    public function doFoo()
    {
        $siteTreeDataList = SiteTree::get();
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->filter(array("ID" => "1"))
        );

        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->filterAny(array("ID" => "1"))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->reverse()
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->where("1 = 1")
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->whereAny("1 = 1")
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->innerJoin("1 = 1", "TableName")
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->sort(array("ID", "Title"))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->limit(10)
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->exclude(array("ID" => "1"))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->setDataQueryParam("Versioned.mode", "all_versions")
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->alterDataQuery(function ($query) {
                $query->reverseSort();
            })
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->setQueriedColumns(array("ID"))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->byIDs(array(3,5))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->addMany(array(1,2))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->removeMany(array(1,2))
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->removeByFilter('"ID" = 1')
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            $siteTreeDataList->removeAll()
        );
        die;
    }
}
