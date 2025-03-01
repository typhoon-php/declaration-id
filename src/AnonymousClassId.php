<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 * @template-covariant TObject of object = object
 * @implements ClassId<TObject>
 */
final class AnonymousClassId implements ClassId
{
    /**
     * @param non-empty-string $file
     * @param positive-int $line
     * @param positive-int $column
     */
    public function __construct(
        public readonly string $file,
        public readonly int $line,
        public readonly int $column,
    ) {}

    public function accept(DeclarationIdVisitor $visitor): mixed
    {
        return $visitor->anonymousClass($this);
    }
}
