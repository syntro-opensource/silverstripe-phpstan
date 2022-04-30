<?php

namespace Syntro\SilverstripePHPStan\Tests\Rule\Data;

use SilverStripe\Core\Config\Configurable;

class Foo
{
    use Configurable;

    private static $this_should_be_config = 'a value';

    /**
     * @config
     */
    private static $this_is_config = 'another value';
}
