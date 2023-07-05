<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Type;

use Exception;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extensible;
use Syntro\SilverstripePHPStan\ClassHelper;
use Syntro\SilverstripePHPStan\ConfigHelper;
use Syntro\SilverstripePHPStan\Utility;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ThisType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\UnionType;

class ExtensionReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    protected $ownerClassNamesByExtensionClassName = null;

    public function getClass(): string
    {
        return ClassHelper::Extension;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'getOwner':
                return true;
        }
        return false;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();

        switch ($name) {
            // NOTE(Jake): 2018-04-25
            //
            // Ideally this would work with both '$this->owner' and '$this->getOwner()'
            //
            // However there doesn't seem to be a `DynamicPropertyReturnTypeExtension` hook and I'm
            // not sure on how I can apply that type info.
            //
            case 'getOwner':
                // Get the type of the `Extension` subclass
                $type = $scope->getType($methodCall->var);
                $objectType = null;
                if ($type instanceof ThisType) {
                    $objectType = new ObjectType($type->getClassName());
                } else {
                    $objectType = Utility::getTypeFromVariable($methodCall->var, $methodReflection);
                }
                // NOTE(mleutenegger): 2019-11-10
                // $objectType is always truthy
                //
                // if (!$objectType) {
                //     return $methodReflection->getReturnType();
                // }
                if (!($objectType instanceof ObjectType)) {
                    throw new Exception('Unexpected type: '.get_class($objectType).', expected ObjectType');
                }

                // Lookup if this extension is configured by any class to be used in their 'extensions'
                $extensionClassName = $objectType->getClassName();
                $ownerClassNamesByExtensionClassName = $this->getOwnerClassNamesByExtensionClassName();
                if (!isset($ownerClassNamesByExtensionClassName[$extensionClassName])) {
                    return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
                }
                $classesUsingExtension = $ownerClassNamesByExtensionClassName[$extensionClassName];

                //
                $types = [];
                if ($classesUsingExtension) {
                    foreach ($classesUsingExtension as $class) {
                        // Ignore classes that don't exist.
                        if (!class_exists($class)) {
                            continue;
                        }
                        $types[] = new ObjectType($class);
                    }
                }
                if (!$types) {
                    return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
                }
                if (count($types) === 1) {
                    // NOTE(Jake): 2018-04-25
                    //
                    // UnionType does not allow multiple types to be passed in
                    //
                    return $types[0];
                }
                return new UnionType($types);

            default:
                throw new Exception('Unhandled method call: '.$name);
        }
    }

    private function getOwnerClassNamesByExtensionClassName()
    {
        if ($this->ownerClassNamesByExtensionClassName !== null) {
            return $this->ownerClassNamesByExtensionClassName;
        }
        $extensionToClassName = array();
        $classes = $this->getClassesUsingExtensibleTrait();
        foreach ($classes as $class) {
            $extensions = ConfigHelper::get($class, 'extensions');
            if (!$extensions) {
                continue;
            }
            foreach ($extensions as $extension) {
                if (!$extension) {
                    continue;
                }
                $extensionToClassName[$extension][$class] = $class;
            }
        }
        return $this->ownerClassNamesByExtensionClassName = $extensionToClassName;
    }

    private function getClassesUsingExtensibleTrait()
    {
        $classes = ClassInfo::allClasses();
        $result = array();
        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $traits = $reflection->getTraits();
            if (in_array(Extensible::class, $traits)) {
                foreach (ClassInfo::subclassesFor($class, true) as $subclass) {
                    $result[$subclass] = $subclass;
                }
            }
        }
        $result = array_values($result);
        return $result;
    }
}
