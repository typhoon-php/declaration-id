<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId\Internal;

/**
 * @internal
 * @psalm-internal Typhoon\DeclarationId
 * @return positive-int
 */
function columnAtPosition(string $string, int $position): int
{
    \assert($position <= \strlen($string));

    if (preg_match('/(?>\r\n|\n|\r)(.*)$/', substr($string, 0, $position), $matches) === 1) {
        return \strlen($matches[1]) + 1;
    }

    return 1;
}
