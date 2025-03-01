<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 */
final class ParameterId implements DeclarationId
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public readonly FunctionId $functionId,
        public readonly string $name,
    ) {}

    public function accept(DeclarationIdVisitor $visitor): mixed
    {
        return $visitor->parameter($this);
    }
}
