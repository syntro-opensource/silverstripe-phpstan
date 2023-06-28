<?php

namespace InjectorDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Assets\File;
use SilverStripe\Control\Cookie_Backend;
use SilverStripe\Control\CookieJar;
use SilverStripe\ORM\Connect\MySQLDatabase;
use Syntro\SilverstripePHPStan\ClassHelper;
use function PHPStan\Testing\assertType;

class Foo
{
    public function doFoo()
    {
        $sitetree = new SiteTree();
        assertType(
            File::class,
            Injector::inst()->get(File::class)
        );
        assertType(
            CookieJar::class,
            Injector::inst()->get(Cookie_Backend::class)
        );
        assertType(
            MySQLDatabase::class,
            Injector::inst()->get("MySQLDatabase")
        );

        assertType(
            File::class,
            singleton(File::class)
        );
        assertType(
            CookieJar::class,
            singleton(Cookie_Backend::class)
        );
        assertType(
            MySQLDatabase::class,
            singleton("MySQLDatabase")
        );
        die;
    }
}
