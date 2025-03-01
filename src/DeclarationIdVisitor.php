<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 * @template-covariant TResult
 */
interface DeclarationIdVisitor
{
    /**
     * @return TResult
     */
    public function constant(ConstantId $constantId): mixed;

    /**
     * @return TResult
     */
    public function anonymousFunction(AnonymousFunctionId $functionId): mixed;

    /**
     * @return TResult
     */
    public function namedFunction(NamedFunctionId $functionId): mixed;

    /**
     * @return TResult
     */
    public function anonymousClass(AnonymousClassId $classId): mixed;

    /**
     * @return TResult
     */
    public function namedClass(NamedClassId $classId): mixed;

    /**
     * @return TResult
     */
    public function classConstant(ClassConstantId $constantId): mixed;

    /**
     * @return TResult
     */
    public function property(PropertyId $propertyId): mixed;

    /**
     * @return TResult
     */
    public function method(MethodId $methodId): mixed;

    /**
     * @return TResult
     */
    public function parameter(ParameterId $parameterId): mixed;

    /**
     * @return TResult
     */
    public function alias(AliasId $aliasId): mixed;

    /**
     * @return TResult
     */
    public function template(TemplateId $templateId): mixed;
}
