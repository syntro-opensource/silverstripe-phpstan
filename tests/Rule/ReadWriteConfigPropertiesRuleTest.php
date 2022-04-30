<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Rule;

use Syntro\SilverstripePHPStan\Rule\ReadWriteConfigPropertiesRule;
use PHPStan\Rules\Rule;

class ReadWriteConfigPropertiesRuleTest extends \PHPStan\Testing\RuleTestCase
{

    protected function getRule(): Rule
    {
        return new ReadWriteConfigPropertiesRule();
    }

    public function testAddsHintsForConfigurable(): void
    {
        $this->analyse([__DIR__ . '/Data/ReadWritePropertiesConfig.php'], [
            [
                'Never read, only written static property Syntro\SilverstripePHPStan\Tests\Rule\Data\Foo::$this_should_be_config is set on a configurable class. Have you forgotten to add "@config"?',
                11,
            ],
        ]);
    }

    public function testAddsNoHintsForNoConfigurable(): void
    {
        $this->analyse([__DIR__ . '/Data/ReadWritePropertiesNoConfig.php'], []);
    }
}
