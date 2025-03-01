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
function findAnonymousFunctionTokens(array $tokens, int $line): array
{
    $staticToken = null;
    $functionToken = null;
    $foundTokens = [];

    foreach ($tokens as $token) {
        if ($token->line > $line && ($staticToken === null && $functionToken === null)) {
            break;
        }

        if ($token->isIgnorable()) {
            continue;
        }

        if ($token->line === $line && $token->is(T_STATIC)) {
            $staticToken = $token;
            $functionToken = null;

            continue;
        }

        if (($token->line === $line || $staticToken !== null) && $token->is(T_FN)) {
            $foundTokens[] = $staticToken ?? $token;
            $staticToken = null;
            $functionToken = null;

            continue;
        }

        if (($token->line === $line || $staticToken !== null) && $token->is(T_FUNCTION)) {
            $functionToken = $token;

            continue;
        }

        if ($functionToken !== null && $token->text === '(') {
            $foundTokens[] = $staticToken ?? $functionToken;
            $staticToken = null;
            $functionToken = null;

            continue;
        }

        $staticToken = null;
        $functionToken = null;
    }

    return $foundTokens;
}
