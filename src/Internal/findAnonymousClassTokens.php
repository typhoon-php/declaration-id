<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId\Internal;

/**
 * @internal
 * @psalm-internal Typhoon\DeclarationId
 * @param array<\PhpToken> $tokens
 * @param positive-int $line
 * @return list<\PhpToken>
 */
function findAnonymousClassTokens(array $tokens, int $line): array
{
    $previousTokenIsNew = false;
    $foundTokens = [];

    foreach ($tokens as $token) {
        if ($token->line > $line) {
            break;
        }

        if ($token->isIgnorable()) {
            continue;
        }

        if ($token->line === $line && $previousTokenIsNew && $token->is(T_CLASS)) {
            $previousTokenIsNew = false;
            $foundTokens[] = $token;

            continue;
        }

        $previousTokenIsNew = $token->is(T_NEW);
    }

    return $foundTokens;
}
