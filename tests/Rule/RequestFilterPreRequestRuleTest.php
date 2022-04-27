<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Rule;

use Syntro\SilverstripePHPStan\Rule\RequestFilterPreRequestRule;
use Syntro\SilverstripePHPStan\Tests\ResolverTest;
use PHPStan\Rules\Rule;

class RequestFilterPreRequestRuleTest extends \PHPStan\Testing\RuleTestCase
{

    protected function getRule(): Rule
    {
        return new RequestFilterPreRequestRule();
    }

    public function testRequestFilterGood(): void
    {
        $this->analyse([__DIR__ . '/Data/RequestFilterGood.php'], []);
    }

    public function testRequestFilterBad(): void
    {
        $this->analyse([__DIR__ . '/Data/RequestFilterBad.php'], [
            [
                'SilverStripe\Control\RequestFilter::preRequest() should not return false as this will cause an uncaught "Invalid Request" exception to be thrown by the SilverStripe framework. (returning "null" will not cause this problem)',
                19,
            ],
        ]);
    }
}
