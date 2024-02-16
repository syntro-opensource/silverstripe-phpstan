<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Type;

use Exception;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Syntro\SilverstripePHPStan\ClassHelper;
use Syntro\SilverstripePHPStan\ConfigHelper;
use Syntro\SilverstripePHPStan\Utility;

class DataObjectReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ClassHelper::DataObject;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'getCMSFields':
                return true;

            case 'dbObject':
                return true;

            case 'newClassInstance':
                return true;
        }
        return false;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $type = $scope->getType($methodCall->var);

        $name = $methodReflection->getName();
        switch ($name) {
            case 'getCMSFields':
                // todo(Jake): 2018-04-29
                //
                // This is very incomplete.
                //
                // Look into determining the values of a FieldList based on scaffolding
                // and SiteTree defaults. This is so `GridField` and other FormField
                // subclasses can be reasoned about.
                //
                // A current blocker in PHPStan 0.9.2 is that `parent::getCMSFields()`
                // won't work with `DataObjectReturnTypeExtension` but if I call it
                // directly from another function like `$this->getCMSFields()`, this will
                // execute.
                //
                $objectType = Utility::getMethodReturnType($methodReflection);
                if (!($objectType instanceof ObjectType)) {
                    throw new Exception('Unexpected type: '.get_class($objectType).', expected ObjectType');
                }
                $className = $objectType->getClassName();
                return new FieldListType($className);

            case 'dbObject':
                $className = '';
                if ($type instanceof UnionType) {
                    $type = array_filter($type->getTypes(), fn ($subType) => $subType instanceof ObjectType)[0] ?? null;
                }
                if ($type instanceof StaticType) {
                    if (count($type->getReferencedClasses()) === 1) {
                        $className = $type->getReferencedClasses()[0];
                    }
                } elseif ($type instanceof ObjectType) {
                    $className = $type->getClassName();
                }
                if (!$className) {
                    throw new Exception('Unhandled type: '.get_class($type));
                    //return Utility::getMethodReturnType($methodReflection);
                }
                if (count($methodCall->args) === 0) {
                    return Utility::getMethodReturnType($methodReflection);
                }
                // Handle $this->dbObject('Field')
                $arg = $methodCall->args[0]->value;
                $fieldName = '';
                if ($arg instanceof Variable) {
                    // Unhandled, cannot retrieve variable value even if set in this scope.
                    return Utility::getMethodReturnType($methodReflection);
                } else if ($arg instanceof ClassConstFetch) {
                    // Handle "SiteTree::class" constant
                    $fieldName = (string)$arg->class;
                } else if ($arg instanceof String_) {
                    $fieldName = $arg->value;
                }
                if (!$fieldName) {
                    throw new Exception('Mishandled "newClassInstance" call.');
                    //return Utility::getMethodReturnType($methodReflection);
                }
                $dbFields = ConfigHelper::get_db($className);
                if (!isset($dbFields[$fieldName])) {
                    return Utility::getMethodReturnType($methodReflection);
                }
                $dbFieldType = $dbFields[$fieldName];
                // NOTE(mleutenegger): 2019-11-10
                // $dbFieldType is always truthy
                //
                // if (!$dbFieldType) {
                //     return Utility::getMethodReturnType($methodReflection);
                // }
                return $dbFieldType;

            case 'newClassInstance':
                if (count($methodCall->args) === 0) {
                    return Utility::getMethodReturnType($methodReflection);
                }
                $arg = $methodCall->args[0]->value;
                $type = Utility::getTypeFromVariable($arg, $methodReflection);
                return $type;

            default:
                throw new Exception('Unhandled method call: '.$name);
        }
    }
}
