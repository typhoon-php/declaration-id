<?php

declare(strict_types=1);

namespace Typhoon\DeclarationId\Internal;

use Typhoon\DeclarationId\Id;

/**
 * @internal
 * @psalm-internal Typhoon
 * @template TId of Id
 * @template-covariant TValue
 * @psalm-suppress InvalidTemplateParam
 * @implements \ArrayAccess<TId, TValue>
 * @implements \IteratorAggregate<TId, TValue>
 */
final class IdMap implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var array<non-empty-string, array{TId, TValue}>
     */
    private array $values = [];

    /**
     * @param iterable<TId, TValue> $values
     */
    public function __construct(iterable $values = [])
    {
        foreach ($values as $id => $value) {
            $this->values[$id->encode()] = [$id, $value];
        }
    }

    /**
     * @return list<TId>
     */
    public function ids(): array
    {
        return array_column($this->values, 0);
    }

    /**
     * @template TNewId of Id
     * @template TNewValue
     * @param TNewId $id
     * @param TNewValue $value
     * @return self<TId|TNewId, TValue|TNewValue>
     */
    public function with(Id $id, mixed $value): self
    {
        /** @var self<TId|TNewId, TValue|TNewValue> */
        $copy = clone $this;
        $copy->values[$id->encode()] = [$id, $value];

        return $copy;
    }

    /**
     * @param TId $id
     * @return self<TId, TValue>
     */
    public function without(Id $id): self
    {
        $copy = clone $this;
        unset($copy->values[$id->encode()]);

        return $copy;
    }

    /**
     * @template TNewId of Id
     * @template TNewValue
     * @param IdMap<TNewId, TNewValue> $idMap
     * @return self<TId|TNewId, TValue|TNewValue>
     */
    public function withMap(self $idMap): self
    {
        /** @var self<TId|TNewId, TValue|TNewValue> */
        $copy = clone $this;
        $copy->values = [...$copy->values, ...$idMap->values];

        return $copy;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset->encode()]);
    }

    /**
     * @param TId $offset
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset->encode()][1] ?? throw new IdIsNotDefined($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException();
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException();
    }

    /**
     * @template TNewValue
     * @param callable(TValue, TId): TNewValue $mapper
     * @return self<TId, TNewValue>
     */
    public function map(callable $mapper): self
    {
        $copy = clone $this;

        foreach ($copy->values as [$id, &$_value]) {
            $_value = $mapper($_value, $id);
        }

        /** @var self<TId, TNewValue> */
        return $copy;
    }

    /**
     * @return self<TId, TValue>
     */
    public function slice(int $offset = 0, ?int $limit = null): self
    {
        $copy = clone $this;
        $copy->values = \array_slice($this->values, $offset, $limit);

        return $copy;
    }

    /**
     * @return \Generator<TId, TValue>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->values as [$id, $value]) {
            yield $id => $value;
        }
    }

    /**
     * @return non-negative-int
     */
    public function count(): int
    {
        return \count($this->values);
    }
}
