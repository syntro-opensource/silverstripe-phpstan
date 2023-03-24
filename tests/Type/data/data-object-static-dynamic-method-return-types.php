<?php

namespace DataObjectStaticDynamicMethodReturnTypesNamespace;

use PHPStan\Type\NullType;
use PHPStan\Type\UnionType;
use PHPStan\Type\UnionTypeHelper;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Syntro\SilverstripePHPStan\ClassHelper;
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
            sprintf('%s|null', ClassHelper::SiteTree),
            DataObject::get_one(SiteTree::class)
        );

        assertType(
            sprintf('%s', ClassHelper::HTMLText),
            DBField::create_field(DBHTMLText::class)
        );
        // assertType(
        //     sprintf('%s', ClassHelper::HTMLText),
        //     DataObject::create_field(sprintf('%s', ClassHelper::HTMLText)::class)
        // );

        assertType(
            sprintf('%s|null', ClassHelper::SiteTree),
            SiteTree::get_one(SiteTree::class)
        );
        assertType(
            sprintf('%s|null', ClassHelper::SiteTree),
            SiteTree::get_by_id(1)
        );
        die;
    }
}
