<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId\Internal;

/**
 * @internal
 * @psalm-internal Typhoon\DeclarationId
 * @psalm-immutable
 */
final class AnonymousClassName
{
    public static function tryFromString(string $name): ?self
    {
        if (preg_match('/^(.+)@anonymous\x00(.+):(\d+)\$(\w+)$/', $name, $matches) !== 1) {
            return null;
        }

        /** @var ?non-empty-string */
        $supertype = $matches[1] === 'class' ? null : $matches[1];

        /** @var non-empty-string */
        $file = $matches[2];

        $line = (int) $matches[3];
        \assert($line > 0);

        $runtimeIndex = hexdec($matches[4]);
        \assert(\is_int($runtimeIndex) && $runtimeIndex >= 0);

        return new self(
            supertype: $supertype,
            file: $file,
            line: $line,
            runtimeIndex: $runtimeIndex,
        );
    }

    /**
     * @param ?non-empty-string $supertype
     * @param non-empty-string $file
     * @param positive-int $line
     * @param non-negative-int $runtimeIndex
     */
    private function __construct(
        public readonly ?string $supertype,
        public readonly string $file,
        public readonly int $line,
        public readonly int $runtimeIndex,
    ) {}

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return non-empty-string
     */
    public function toString(): string
    {
        return \sprintf(
            "%s@anonymous\x00%s:%d$%x",
            $this->supertype ?? 'class',
            $this->file,
            $this->line,
            $this->runtimeIndex,
        );
    }
}
