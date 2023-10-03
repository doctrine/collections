<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Closure;
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
    protected Collection|null $collection;

    protected bool $initialized = false;

    public function count(): int
    {
        $this->initialize();

        return $this->collection->count();
    }

    public function add(mixed $element): void
    {
        $this->initialize();

        $this->collection->add($element);
    }

    public function clear(): void
    {
        $this->initialize();
        $this->collection->clear();
    }

    public function contains(mixed $element): bool
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

    public function removeElement(mixed $element): bool
    {
        $this->initialize();

        return $this->collection->removeElement($element);
    }

    public function containsKey(string|int $key): bool
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

    public function exists(Closure $p): bool
    {
        $this->initialize();

        return $this->collection->exists($p);
    }

    public function findFirst(Closure $p): mixed
    {
        $this->initialize();

        return $this->collection->findFirst($p);
    }

    public function filter(Closure $p): Collection
    {
        $this->initialize();

        return $this->collection->filter($p);
    }

    public function forAll(Closure $p): bool
    {
        $this->initialize();

        return $this->collection->forAll($p);
    }

    public function map(Closure $func): Collection
    {
        $this->initialize();

        return $this->collection->map($func);
    }

    /**
     * {@inheritDoc}
     */
    public function reduce(Closure $func, $initial = null): mixed
    {
        $this->initialize();

        return $this->collection->reduce($func, $initial);
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p): array
    {
        $this->initialize();

        return $this->collection->partition($p);
    }

    /**
     * {@inheritDoc}
     *
     * @template TMaybeContained
     */
    public function indexOf($element): string|int|false
    {
        $this->initialize();

        return $this->collection->indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $offset, int|null $length = null): array
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

    /** @param TKey $offset */
    public function offsetExists(mixed $offset): bool
    {
        $this->initialize();

        return $this->collection->offsetExists($offset);
    }

    /** @param TKey $offset */
    public function offsetGet(mixed $offset): mixed
    {
        $this->initialize();

        return $this->collection->offsetGet($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T         $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /** @param TKey $offset */
    public function offsetUnset(mixed $offset): void
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * Is the lazy collection already initialized?
     *
     * @psalm-assert-if-true Collection<TKey,T> $this->collection
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
