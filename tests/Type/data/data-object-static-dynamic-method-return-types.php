<?php

namespace DataObjectStaticDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;
use Symbiote\SilverstripePHPStan\ClassHelper;
use function PHPStan\Testing\assertType;

class Foo
{
	public function doFoo()
	{
		$sitetree = new SiteTree();
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            SiteTree::get()
        );
        assertType(
            sprintf('%s<%s>', ClassHelper::DataList, ClassHelper::SiteTree),
            DataObject::get(SiteTree::class)
        );
        // // assertType(
        // //     ClassHelper::SiteTree,
        // //     SiteTree::get_one()
        // // );
        assertType(
            sprintf('%s', ClassHelper::SiteTree),
            DataObject::get_one(SiteTree::class)
        );
		die;
	}
}
