<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 * @return (
 *     $reflection is \ReflectionFunction ? NamedFunctionId|AnonymousFunctionId :
 *     $reflection is \ReflectionClass<object> ? NamedClassId|AnonymousClassId :
 *     $reflection is \ReflectionClassConstant ? ClassConstantId :
 *     $reflection is \ReflectionProperty ? PropertyId :
 *     $reflection is \ReflectionMethod ? MethodId :
 *     $reflection is \ReflectionParameter ? ParameterId : never
 * )
 */
function reflectionId(\ReflectionFunctionAbstract|\ReflectionClass|\ReflectionClassConstant|\ReflectionProperty|\ReflectionParameter $reflection): DeclarationId
{
    if ($reflection instanceof \ReflectionFunction) {
        if ($reflection->name === '{closure}') {
            $file = $reflection->getFileName();
            \assert($file !== false, 'Anonymous function reflection should not return false file');

            $line = $reflection->getStartLine();
            \assert($line !== false, 'Anonymous function reflection should not return false line');

            return anonymousFunctionId($file, $line);
        }

        $scopeClass = $reflection->getClosureScopeClass();

        if ($scopeClass !== null) {
            return new MethodId(classId($scopeClass->name), $reflection->name);
        }

        return new NamedFunctionId($reflection->name);
    }

    if ($reflection instanceof \ReflectionClass) {
        return classId($reflection->name);
    }

    if ($reflection instanceof \ReflectionClassConstant) {
        return new ClassConstantId(
            classId: reflectionId($reflection->getDeclaringClass()),
            name: $reflection->name,
        );
    }

    if ($reflection instanceof \ReflectionProperty) {
        if (!$reflection->isDefault()) {
            throw new \InvalidArgumentException('Dynamic property identification is not supported');
        }

        /**
         * @see https://github.com/vimeo/psalm/pull/10091#issuecomment-1670027553
         * @psalm-suppress RedundantCondition
         */
        \assert($reflection->name !== '');

        return new PropertyId(
            classId: reflectionId($reflection->getDeclaringClass()),
            name: $reflection->name,
        );
    }

    if ($reflection instanceof \ReflectionMethod) {
        return new MethodId(
            classId: reflectionId($reflection->getDeclaringClass()),
            name: $reflection->name,
        );
    }

    \assert($reflection instanceof \ReflectionParameter);

    return new ParameterId(
        functionId: reflectionId($reflection->getDeclaringFunction()),
        name: $reflection->name,
    );
}
