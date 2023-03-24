<?php declare(strict_types = 1);

namespace Syntro\SilverstripePHPStan\Type;

use Exception;
use PhpParser\Node\Expr\PropertyFetch;
use Syntro\SilverstripePHPStan\ClassHelper;
use Syntro\SilverstripePHPStan\Utility;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;

class DataObjectGetStaticReturnTypeExtension implements \PHPStan\Type\DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ClassHelper::DataObject;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        $name = $methodReflection->getName();
        return $name === 'get' ||
               $name === 'get_one' ||
               $name === 'get_by_id';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        $name = $methodReflection->getName();
        switch ($name) {
            case 'get':
                if (count($methodCall->args) > 0) {
                    // Handle DataObject::get('Page')
                    $arg = $methodCall->args[0];
                    $type = Utility::getTypeFromInjectorVariable($arg, new ObjectType('SilverStripe\ORM\DataObject'));
                    return new DataListType(ClassHelper::DataList, $type);
                }


                // Handle indirect access like $this->class::get()
                if ($methodCall->class->getType() === 'Expr_PropertyFetch') {
                    $type = $scope->getType($methodCall->class);
                    return new DataListType(ClassHelper::DataList, $scope->getType($methodCall->class));
                }

                // Classes accessed through variables
                // $foo = Page::class;
                // $foo::get();
                if ($methodCall->class instanceof Variable) {
                    $type = $scope->getType($methodCall->class);
                    if ($type instanceof ConstantStringType) {
                        return new DataListType(ClassHelper::DataList, new ObjectType($type->getValue()));
                    } elseif ($type instanceof UnionType) {
                        $types = array_map(function ($type) {
                            if ($type instanceof ConstantStringType) {
                                return new ObjectType($type->getValue());
                            }
                            return $type;
                        }, $type->getTypes());
                        return new DataListType(ClassHelper::DataList, TypeCombinator::union(...$types));
                    } else {
                        // Fallback..
                        return new DataListType(ClassHelper::DataList, new ObjectType(ClassHelper::DataObject));
                    }
                }

                // Handle Page::get() / self::get()
                $callerClass = $methodCall->class->toString();

                if ($callerClass === 'static') {
                    return Utility::getMethodReturnType($methodReflection);
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                return new DataListType(ClassHelper::DataList, new ObjectType($callerClass));

            case 'get_one':
                if (count($methodCall->args) > 0) {
                    // Handle DataObject::get_one('Page')
                    $arg = $methodCall->args[0];
                    $type = Utility::getTypeFromVariable($arg, $methodReflection);
                    return TypeCombinator::addNull($type);
                }
                // Handle Page::get() / self::get()
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return TypeCombinator::addNull(Utility::getMethodReturnType($methodReflection));
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                // get_one is nullable according to SS 4.x.x
                // https://api.silverstripe.org/4/SilverStripe/ORM/DataObject.html#method_get_one
                return TypeCombinator::addNull(new ObjectType($callerClass));
            case 'get_by_id':
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return TypeCombinator::addNull(Utility::getMethodReturnType($methodReflection));
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }

                // get_by_id is nullable according to SS 4.x.x
                // https://api.silverstripe.org/4/SilverStripe/ORM/DataObject.html#method_get_by_id
                return TypeCombinator::addNull(new ObjectType($callerClass));
        }
        // NOTE(mleutenegger): 2019-11-10
        // taken from https://github.com/phpstan/phpstan#dynamic-return-type-extensions
        if (count($methodCall->args) === 0) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $methodCall->args,
                $methodReflection->getVariants()
            )->getReturnType();
        }
        $arg = $methodCall->args[0]->value;

        return $scope->getType($arg);
    }
}
