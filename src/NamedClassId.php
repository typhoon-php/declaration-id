<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 * @template-covariant TObject of object = object
 * @implements ClassId<TObject>
 */
final class NamedClassId implements ClassId
{
    /**
     * @param class-string<TObject> $name
     */
    public function __construct(
        public readonly string $name,
    ) {
        if (str_contains($name, '@')) {
            throw new \InvalidArgumentException();
        }
    }

    public function accept(DeclarationIdVisitor $visitor): mixed
    {
        return $visitor->namedClass($this);
    }
}
