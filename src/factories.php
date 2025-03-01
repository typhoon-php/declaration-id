<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

use Typhoon\DeclarationId\Internal\AnonymousClassName;
use function Typhoon\DeclarationId\Internal\columnAtPosition;
use function Typhoon\DeclarationId\Internal\findAnonymousClassTokens;
use function Typhoon\DeclarationId\Internal\findAnonymousFunctionTokens;

/**
 * @api
 * @param non-empty-string $name
 */
function constantId(string $name): ConstantId
{
    return new ConstantId($name);
}

/**
 * @api
 * @param non-empty-string $name
 */
function namedFunctionId(string $name): NamedFunctionId
{
    return new NamedFunctionId($name);
}

/**
 * @api
 * @param non-empty-string $file
 * @param positive-int $line
 * @param ?positive-int $column
 */
function anonymousFunctionId(string $file, int $line, ?int $column = null): AnonymousFunctionId
{
    if ($column !== null) {
        return new AnonymousFunctionId($file, $line, $column);
    }

    $code = file_get_contents($file);
    \assert($code !== false);

    $tokens = findAnonymousFunctionTokens(\PhpToken::tokenize($code), $line);

    if (\count($tokens) === 1) {
        return new AnonymousFunctionId($file, $line, columnAtPosition($code, $tokens[0]->pos));
    }

    throw new \LogicException('No or multiple anonymous functions on line');
}

/**
 * @api
 * @template TObject of object
 * @param class-string<TObject> $name
 * @return NamedClassId<TObject>|AnonymousClassId<TObject>
 */
function classId(string $name): NamedClassId|AnonymousClassId
{
    $parsedName = AnonymousClassName::tryFromString($name);

    if ($parsedName === null) {
        return new NamedClassId($name);
    }

    $code = file_get_contents($parsedName->file);
    \assert($code !== false);

    $tokens = findAnonymousClassTokens(\PhpToken::tokenize($code), $parsedName->line);

    if (\count($tokens) === 0) {
        throw new \LogicException('No anonymous classes on line');
    }

    if (\count($tokens) === 1) {
        /** @var AnonymousClassId<TObject> */
        return new AnonymousClassId(
            file: $parsedName->file,
            line: $parsedName->line,
            column: columnAtPosition($code, $tokens[0]->pos),
        );
    }

    $index = 0;

    foreach (get_declared_classes() as $declaredClass) {
        if ($declaredClass === $name) {
            /** @var AnonymousClassId<TObject> */
            return new AnonymousClassId(
                file: $parsedName->file,
                line: $parsedName->line,
                column: columnAtPosition($code, $tokens[$index]->pos),
            );
        }

        $declaredParsedName = AnonymousClassName::tryFromString($declaredClass);

        if ($declaredParsedName !== null
            && $declaredParsedName->file === $parsedName->file
            && $declaredParsedName->line === $parsedName->line
        ) {
            ++$index;
        }
    }

    throw new \LogicException();
}

/**
 * @api
 * @template TObject of object
 * @param class-string<TObject> $name
 * @return NamedClassId<TObject>
 */
function namedClassId(string $name): NamedClassId
{
    return new NamedClassId($name);
}

/**
 * @api
 * @param non-empty-string $file
 * @param positive-int $line
 * @param ?positive-int $column
 */
function anonymousClassId(string $file, int $line, ?int $column = null): AnonymousClassId
{
    if ($column !== null) {
        return new AnonymousClassId(
            file: $file,
            line: $line,
            column: $column,
        );
    }

    $code = file_get_contents($file);
    \assert($code !== false);

    $tokens = findAnonymousClassTokens(\PhpToken::tokenize($code), $line);

    if (\count($tokens) === 1) {
        return new AnonymousClassId($file, $line, columnAtPosition($code, $tokens[0]->pos));
    }

    throw new \LogicException('No or multiple anonymous classes on line');
}

/**
 * @api
 * @param class-string|ClassId $classId
 * @param non-empty-string $name
 */
function classConstantId(string|ClassId $classId, string $name): ClassConstantId
{
    if (\is_string($classId)) {
        $classId = classId($classId);
    }

    return new ClassConstantId($classId, $name);
}

/**
 * @api
 * @param class-string|ClassId $classId
 * @param non-empty-string $name
 */
function propertyId(string|ClassId $classId, string $name): PropertyId
{
    if (\is_string($classId)) {
        $classId = classId($classId);
    }

    return new PropertyId($classId, $name);
}

/**
 * @api
 * @param class-string|ClassId $classId
 * @param non-empty-string $name
 */
function methodId(string|ClassId $classId, string $name): MethodId
{
    if (\is_string($classId)) {
        $classId = classId($classId);
    }

    return new MethodId($classId, $name);
}

/**
 * @api
 * @param non-empty-string $name
 */
function parameterId(FunctionId $functionId, string $name): ParameterId
{
    return new ParameterId($functionId, $name);
}

/**
 * @api
 * @param non-empty-string $name
 */
function aliasId(ClassId $classId, string $name): AliasId
{
    return new AliasId($classId, $name);
}

/**
 * @api
 * @param non-empty-string $name
 */
function templateId(FunctionId|ClassId $declarationId, string $name): TemplateId
{
    return new TemplateId($declarationId, $name);
}
