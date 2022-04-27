<?php

namespace DataObjectDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;
use Syntro\SilverstripePHPStan\ClassHelper;
use function PHPStan\Testing\assertType;

class Foo
{
    public function doFoo()
    {
        $sitetree = new SiteTree();
        assertType(
            sprintf('%s', ClassHelper::DBInt),
            $sitetree->dbObject("ID")
        );
        assertType(
            sprintf('%s', ClassHelper::HTMLText),
            $sitetree->dbObject("Content")
        );
        die;
    }
}
