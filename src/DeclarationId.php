<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId;

/**
 * @api
 */
interface DeclarationId
{
    /**
     * @template TResult
     * @param DeclarationIdVisitor<TResult> $visitor
     * @return TResult
     */
    public function accept(DeclarationIdVisitor $visitor): mixed;
}
