<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Tests\Rule;

use Syntro\SilverstripePHPStan\Rule\ReadWriteConfigPropertiesRule;
use Syntro\SilverstripePHPStan\Reflection\ReadWritePropertiesExtension;
use Syntro\SilverstripePHPStan\Tests\Rule\DirectReadWritePropertiesExtensionProvider;
use PHPStan\Rules\Rule;

class ReadWriteConfigPropertiesRuleTest extends \PHPStan\Testing\RuleTestCase
{

    protected function getRule(): Rule
    {
        return new ReadWriteConfigPropertiesRule(
            new DirectReadWritePropertiesExtensionProvider([new ReadWritePropertiesExtension()]),
            [],
            [],
            true
        );
    }

    public function testAddsHintsForConfigurable(): void
    {
        $this->analyse([__DIR__ . '/Data/ReadWritePropertiesConfig.php'], [
            [
                'Have you forgotten to add "@config" for the property $this_should_be_config of the configurable class Syntro\SilverstripePHPStan\Tests\Rule\Data\Foo?',
                11,
            ],
        ]);
    }

    public function testAddsNoHintsForNoConfigurable(): void
    {
        $this->analyse([__DIR__ . '/Data/ReadWritePropertiesNoConfig.php'], []);
    }
}
