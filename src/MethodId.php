<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 */
final class MethodId implements FunctionId
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public readonly ClassId $classId,
        public readonly string $name,
    ) {}

    public function accept(DeclarationIdVisitor $visitor): mixed
    {
        return $visitor->method($this);
    }
}
