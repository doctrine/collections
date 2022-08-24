<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use LogicException;
use Traversable;

/**
 * Lazy collection that is backed by a concrete collection
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-implements Collection<TKey,T>
 */
abstract class AbstractLazyCollection implements Collection
{
    /**
     * The backed collection to use
     *
     * @psalm-var Collection<TKey,T>|null
     * @var Collection<mixed>|null
     */
    protected ?Collection $collection;

    protected bool $initialized = false;

    public function count(): int
    {
        $this->initialize();

        return $this->collection->count();
    }

    public function add(mixed $element)
    {
        $this->initialize();

        $this->collection->add($element);
    }

    public function clear(): void
    {
        $this->initialize();
        $this->collection->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element): bool
    {
        $this->initialize();

        return $this->collection->contains($element);
    }

    public function isEmpty(): bool
    {
        $this->initialize();

        return $this->collection->isEmpty();
    }

    public function remove(string|int $key): mixed
    {
        $this->initialize();

        return $this->collection->remove($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element): bool
    {
        $this->initialize();

        return $this->collection->removeElement($element);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key): bool
    {
        $this->initialize();

        return $this->collection->containsKey($key);
    }

    public function get(string|int $key): mixed
    {
        $this->initialize();

        return $this->collection->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys(): array
    {
        $this->initialize();

        return $this->collection->getKeys();
    }

    /**
     * {@inheritDoc}
     */
    public function getValues(): array
    {
        $this->initialize();

        return $this->collection->getValues();
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value): void
    {
        $this->initialize();
        $this->collection->set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $this->initialize();

        return $this->collection->toArray();
    }

    public function first(): mixed
    {
        $this->initialize();

        return $this->collection->first();
    }

    public function last(): mixed
    {
        $this->initialize();

        return $this->collection->last();
    }

    public function key(): string|int|null
    {
        $this->initialize();

        return $this->collection->key();
    }

    public function current(): mixed
    {
        $this->initialize();

        return $this->collection->current();
    }

    public function next(): mixed
    {
        $this->initialize();

        return $this->collection->next();
    }

    public function exists(callable $p): bool
    {
        $this->initialize();

        return $this->collection->exists($p);
    }

    public function findFirst(callable $p): mixed
    {
        $this->initialize();

        return $this->collection->findFirst($p);
    }

    /**
     * @psalm-param callable(T=, TKey=):bool $p
     *
     * @return Collection<mixed>
     * @psalm-return Collection<TKey, T>
     */
    public function filter(callable $p): Collection
    {
        $this->initialize();

        return $this->collection->filter($p);
    }

    public function forAll(callable $p): bool
    {
        $this->initialize();

        return $this->collection->forAll($p);
    }

    /**
     * @psalm-param callable(T=):U $func
     *
     * @return Collection<mixed>
     * @psalm-return Collection<TKey, U>
     *
     * @psalm-template U
     */
    public function map(callable $func): Collection
    {
        $this->initialize();

        return $this->collection->map($func);
    }

    /**
     * {@inheritDoc}
     */
    public function reduce(callable $func, $initial = null): mixed
    {
        $this->initialize();

        return $this->collection->reduce($func, $initial);
    }

    /**
     * {@inheritDoc}
     */
    public function partition(callable $p): array
    {
        $this->initialize();

        return $this->collection->partition($p);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element): string|int|false
    {
        $this->initialize();

        return $this->collection->indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $offset, ?int $length = null): array
    {
        $this->initialize();

        return $this->collection->slice($offset, $length);
    }

    /**
     * @return Traversable<int|string, mixed>
     * @psalm-return Traversable<TKey,T>
     */
    public function getIterator(): Traversable
    {
        $this->initialize();

        return $this->collection->getIterator();
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        $this->initialize();

        return $this->collection->offsetExists($offset);
    }

    /**
     * @param TKey $offset
     */
    public function offsetGet($offset): mixed
    {
        $this->initialize();

        return $this->collection->offsetGet($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T         $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * Is the lazy collection already initialized?
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Initialize the collection
     *
     * @psalm-assert Collection<TKey,T> $this->collection
     */
    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->doInitialize();
        $this->initialized = true;

        if ($this->collection === null) {
            throw new LogicException('You must initialize the collection property in the doInitialize() method.');
        }
    }

    /**
     * Do the initialization logic
     */
    abstract protected function doInitialize(): void;
}
