<?php

namespace Syntro\SilverstripePHPStan\Reflection;

use PHPStan\Reflection\PropertyReflection;
use SilverStripe\Core\Config\Configurable;

/**
 * Adds information about configuration properties, which are always read,
 * written and initialized. We check for the presence of the '@config'
 * docblock as described in the official silverstripe docs
 * (https://docs.silverstripe.org/en/4/developer_guides/configuration/configuration/#configuration-properties)
 * and wether or not the class defining the property uses the 'Configurable' trait.
 */
class ReadWritePropertiesExtension implements \PHPStan\Rules\Properties\ReadWritePropertiesExtension
{

    /**
     * isAlwaysRead - see https://phpstan.org/developing-extensions/always-read-written-properties
     *
     * @param  PropertyReflection $property     the reflection from the static analysis
     * @param  string             $propertyName the name of the property
     * @return bool
     */
    public function isAlwaysRead(PropertyReflection $property, string $propertyName): bool
    {
        return
            $this->containsConfigBlock($property) &&
            $this->classUsesConfigTrait($property);
    }

    /**
     * isAlwaysWritten - see https://phpstan.org/developing-extensions/always-read-written-properties
     *
     * @param  PropertyReflection $property     the reflection from the static analysis
     * @param  string             $propertyName the name of the property
     * @return bool
     */
    public function isAlwaysWritten(PropertyReflection $property, string $propertyName): bool
    {
        return
            $this->containsConfigBlock($property) &&
            $this->classUsesConfigTrait($property);
    }

    /**
     * isInitialized - see https://phpstan.org/developing-extensions/always-read-written-properties
     *
     * @param  PropertyReflection $property     the reflection from the static analysis
     * @param  string             $propertyName the name of the property
     * @return bool
     */
    public function isInitialized(PropertyReflection $property, string $propertyName): bool
    {
        return
            $this->containsConfigBlock($property) &&
            $this->classUsesConfigTrait($property);
    }

    /**
     * containsConfigBlock - check if the @config docblock is present
     *
     * @param  PropertyReflection $property the property to check
     * @return bool
     */
    public function containsConfigBlock(PropertyReflection $property): bool
    {
        return !!strpos($property->getDocComment(), '@config');
    }

    /**
     * classUsesConfigTrait - check if the class defining the property is using
     * the configurable trait
     *
     * @param  PropertyReflection $property the property to check
     * @return bool
     */
    public function classUsesConfigTrait(PropertyReflection $property): bool
    {
        return $property->getDeclaringClass()->hasTraitUse(Configurable::class);
    }
}
