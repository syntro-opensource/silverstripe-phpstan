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
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

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
                    } else {
                        throw new Exception(sprintf("Variable %s can't be resolved to a class name. Try adding a docblock to the variable to describe it's type.", $methodCall->class->name));
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
                    return $type;
                }
                // Handle Page::get() / self::get()
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return Utility::getMethodReturnType($methodReflection);
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                return new ObjectType($callerClass);
            case 'get_by_id':
                $callerClass = $methodCall->class->toString();
                if ($callerClass === 'static') {
                    return Utility::getMethodReturnType($methodReflection);
                }
                if ($callerClass === 'self') {
                    $callerClass = $scope->getClassReflection()->getName();
                }
                return  new ObjectType($callerClass);
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
