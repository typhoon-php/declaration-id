<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId\Internal;

/**
 * @internal
 * @psalm-internal Typhoon\DeclarationId
 * @param non-empty-string $name
 * @return non-empty-string
 */
function escapeNullBytes(string $name): string
{
    /** @var non-empty-string */
    return str_replace("\x00", '\0', $name);
}
