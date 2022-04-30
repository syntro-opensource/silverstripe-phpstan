<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Rule;

use PhpParser\Node;
use PHPStan\ShouldNotHappenException;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassPropertiesNode;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\DeadCode\UnusedPrivatePropertyRule;
use PHPStan\Rules\Properties\ReadWritePropertiesExtensionProvider;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeUtils;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extension;

/**
 * Adds a rule to add a hint to never read, never written or unused properties,
 * instructing the user to add an "@config" docblock if necessary
 */
class ReadWriteConfigPropertiesRule extends UnusedPrivatePropertyRule /* @phpstan-ignore-line */
{

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection->hasTraitUse(Configurable::class) || $classReflection->isSubclassOf(Extension::class)) {
            $errors = parent::processNode($node, $scope); /* @phpstan-ignore-line */
            $hints = [];
            /** @var \PHPStan\Rules\RuleError $error */
            foreach ($errors as $error) {
                $message = $error->getMessage(); /* @phpstan-ignore-line */
                if (strpos($message, 'Static') !== 0 || strpos($message, 'never written, only read') !== false) {
                    // NOTE(mleutenegger): 2022-04-30
                    // In this case, the property wasn't static or the property
                    // was never written, but only read via $this->property.
                    // This should not actually happen with config properties.
                    // In this case, we assume that the error is genuine and
                    // dont print a hint. We might need to handle this case in
                    // the future, when adding config rules.
                    continue;
                }
                $nameFound = preg_match("/(\S+)::(\S+)/", $message, $names);
                if ($nameFound) {
                    $tip = sprintf('See: %s', 'https://docs.silverstripe.org/en/4/developer_guides/configuration/configuration/');
                    $hints[] = RuleErrorBuilder::message(sprintf('Have you forgotten to add "@config" for the property %s of the configurable class %s?', $names[2],$names[1]))->line($error->line)->tip($tip)->build();
                }
            }
            return $hints;
        }
        return [];
    }
}
