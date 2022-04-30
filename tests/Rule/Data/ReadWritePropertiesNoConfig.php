<?php

namespace Syntro\SilverstripePHPStan\Tests\Rule\Data;

class NoFoo
{
    private static $this_should_be_config = 'a value';

    /**
     * @config
     */
    private static $this_is_config = 'another value';
}
