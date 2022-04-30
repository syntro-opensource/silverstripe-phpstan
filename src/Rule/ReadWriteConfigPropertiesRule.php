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

/**
 * Adds a rule to add a hint to never read, never written or unused properties,
 * instructing the user to add an "@config" docblock if necessary
 */
class ReadWriteConfigPropertiesRule implements \PHPStan\Rules\Rule
{


    /**
     * @var ReadWritePropertiesExtensionProvider
     */
    private $extensionProvider;

    /**
     * @var array
     */
    private $alwaysWrittenTags;

    /**
     * @var array
     */
    private $alwaysReadTags;

    /**
     * @var bool
     */
    private $checkUninitializedProperties;

    /**
     * @param string[] $alwaysWrittenTags
     * @param string[] $alwaysReadTags
     */
    public function __construct(
        ReadWritePropertiesExtensionProvider $extensionProvider,
        array $alwaysWrittenTags,
        array $alwaysReadTags,
        bool $checkUninitializedProperties
    )
    {
        $this->extensionProvider = $extensionProvider;
        $this->alwaysWrittenTags = $alwaysWrittenTags;
        $this->alwaysReadTags = $alwaysReadTags;
        $this->checkUninitializedProperties = $checkUninitializedProperties;
    }

    public function getNodeType(): string
    {
        return ClassPropertiesNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->getClass() instanceof Node\Stmt\Class_) {
            return [];
        }
        if (!$scope->isInClass()) {
            throw new ShouldNotHappenException();
        }

        $classReflection = $scope->getClassReflection();
        $classType = new ObjectType($classReflection->getName());

        $properties = [];
        foreach ($node->getProperties() as $property) {
            if (!$property->isPrivate()) {
                continue;
            }

            $alwaysRead = false;
            $alwaysWritten = false;
            if ($property->getPhpDoc() !== null) {
                $text = $property->getPhpDoc();
                foreach ($this->alwaysReadTags as $tag) {
                    if (strpos($text, $tag) === false) {
                        continue;
                    }

                    $alwaysRead = true;
                    break;
                }

                foreach ($this->alwaysWrittenTags as $tag) {
                    if (strpos($text, $tag) === false) {
                        continue;
                    }

                    $alwaysWritten = true;
                    break;
                }
            }

            $propertyName = $property->getName();
            if (!$alwaysRead || !$alwaysWritten) {
                if (!$classReflection->hasNativeProperty($propertyName)) {
                    continue;
                }

                $propertyReflection = $classReflection->getNativeProperty($propertyName);

                foreach ($this->extensionProvider->getExtensions() as $extension) {
                    if ($alwaysRead && $alwaysWritten) {
                        break;
                    }
                    if (!$alwaysRead && $extension->isAlwaysRead($propertyReflection, $propertyName)) {
                        $alwaysRead = true;
                    }
                    if ($alwaysWritten || !$extension->isAlwaysWritten($propertyReflection, $propertyName)) {
                        continue;
                    }

                    $alwaysWritten = true;
                }
            }

            $read = $alwaysRead;
            $written = $alwaysWritten || $property->getDefault() !== null;
            $properties[$propertyName] = [
                'read' => $read,
                'written' => $written,
                'node' => $property,
            ];
        }

        foreach ($node->getPropertyUsages() as $usage) {
            $fetch = $usage->getFetch();
            if ($fetch->name instanceof Node\Identifier) {
                $propertyNames = [$fetch->name->toString()];
            } else {
                $propertyNameType = $usage->getScope()->getType($fetch->name);
                $strings = TypeUtils::getConstantStrings($propertyNameType);
                if (count($strings) === 0) {
                    return [];
                }

                $propertyNames = array_map(static fn (ConstantStringType $type): string => $type->getValue(), $strings);
            }
            if ($fetch instanceof Node\Expr\PropertyFetch) {
                $fetchedOnType = $usage->getScope()->getType($fetch->var);
            } else {
                if (!$fetch->class instanceof Node\Name) {
                    continue;
                }

                $fetchedOnType = $usage->getScope()->resolveTypeByName($fetch->class);
            }

            if ($classType->isSuperTypeOf($fetchedOnType)->no()) {
                continue;
            }
            if ($fetchedOnType instanceof MixedType) {
                continue;
            }

            foreach ($propertyNames as $propertyName) {
                if (!array_key_exists($propertyName, $properties)) {
                    continue;
                }
                if ($usage instanceof PropertyRead) {
                    $properties[$propertyName]['read'] = true;
                } else {
                    $properties[$propertyName]['written'] = true;
                }
            }
        }

        $constructors = [];
        $classReflection = $scope->getClassReflection();
        if ($classReflection->hasConstructor()) {
            $constructors[] = $classReflection->getConstructor()->getName();
        }

        [$uninitializedProperties] = $node->getUninitializedProperties($scope, $constructors, $this->extensionProvider->getExtensions());

        $errors = [];

        if ($classReflection->hasTraitUse(Configurable::class)) {
            foreach ($properties as $name => $data) {
                $propertyNode = $data['node'];
                if ($propertyNode->isStatic()) {
                    $propertyName = sprintf('static property %s::$%s', $scope->getClassReflection()->getDisplayName(), $name);

                    $tip = sprintf('See: %s', 'https://docs.silverstripe.org/en/4/developer_guides/configuration/configuration/');

                    if (!$data['read']) {
                        if (!$data['written']) {
                            $errors[] = RuleErrorBuilder::message(sprintf('Unused %s is set on a configurable class. Have you forgotten to add "@config"?', $propertyName))
                                ->line($propertyNode->getStartLine())
                                ->identifier('deadCode.unusedProperty')
                                ->metadata([
                                    'classOrder' => $node->getClass()->getAttribute('statementOrder'),
                                    'classDepth' => $node->getClass()->getAttribute('statementDepth'),
                                    'classStartLine' => $node->getClass()->getStartLine(),
                                    'propertyName' => $name,
                                ])
                                ->tip($tip)
                                ->build();
                        } else {
                            $errors[] = RuleErrorBuilder::message(sprintf('Never read, only written %s is set on a configurable class. Have you forgotten to add "@config"?', $propertyName))->line($propertyNode->getStartLine())->tip($tip)->build();
                        }
                    } elseif (!$data['written'] && (!array_key_exists($name, $uninitializedProperties) || !$this->checkUninitializedProperties)) {
                        // NOTE(mleutenegger): 2022-04-30
                        // In this case, the property was never written, but only
                        // read via $this->property.
                        // This should not actually happen with config properties.
                        // In this case, we assume that the error is genuine and
                        // dont print a hint. I have left this in as we might
                        // need to handle this case in the future, when adding config
                        // rules.
                        continue;
                    }
                }
            }
        }

        return $errors;
    }

}
