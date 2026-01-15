<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Closure;
use LogicException;
use Override;
use Traversable;

/**
 * Lazy collection that is backed by a concrete collection
 *
 * @phpstan-template TKey of array-key
 * @phpstan-template T
 * @template-implements Collection<TKey,T>
 * @template-implements Selectable<TKey,T>
 */
abstract class AbstractLazyCollection implements Collection, Selectable
{
    /**
     * The backed collection to use
     *
     * @var Collection<TKey,T>|null
     */
    protected Collection|null $collection;

    protected bool $initialized = false;

    #[Override]
    public function count(): int
    {
        $this->initialize();

        return $this->collection->count();
    }

    #[Override]
    public function add(mixed $element): void
    {
        $this->initialize();

        $this->collection->add($element);
    }

    #[Override]
    public function clear(): void
    {
        $this->initialize();
        $this->collection->clear();
    }

    #[Override]
    public function contains(mixed $element): bool
    {
        $this->initialize();

        return $this->collection->contains($element);
    }

    #[Override]
    public function isEmpty(): bool
    {
        $this->initialize();

        return $this->collection->isEmpty();
    }

    #[Override]
    public function remove(string|int $key): mixed
    {
        $this->initialize();

        return $this->collection->remove($key);
    }

    #[Override]
    public function removeElement(mixed $element): bool
    {
        $this->initialize();

        return $this->collection->removeElement($element);
    }

    #[Override]
    public function containsKey(string|int $key): bool
    {
        $this->initialize();

        return $this->collection->containsKey($key);
    }

    #[Override]
    public function get(string|int $key): mixed
    {
        $this->initialize();

        return $this->collection->get($key);
    }

    #[Override]
    public function getKeys(): array
    {
        $this->initialize();

        return $this->collection->getKeys();
    }

    #[Override]
    public function getValues(): array
    {
        $this->initialize();

        return $this->collection->getValues();
    }

    #[Override]
    public function set(string|int $key, mixed $value): void
    {
        $this->initialize();
        $this->collection->set($key, $value);
    }

    #[Override]
    public function toArray(): array
    {
        $this->initialize();

        return $this->collection->toArray();
    }

    #[Override]
    public function first(): mixed
    {
        $this->initialize();

        return $this->collection->first();
    }

    #[Override]
    public function last(): mixed
    {
        $this->initialize();

        return $this->collection->last();
    }

    #[Override]
    public function key(): string|int|null
    {
        $this->initialize();

        return $this->collection->key();
    }

    #[Override]
    public function current(): mixed
    {
        $this->initialize();

        return $this->collection->current();
    }

    #[Override]
    public function next(): mixed
    {
        $this->initialize();

        return $this->collection->next();
    }

    #[Override]
    public function exists(Closure $p): bool
    {
        $this->initialize();

        return $this->collection->exists($p);
    }

    #[Override]
    public function findFirst(Closure $p): mixed
    {
        $this->initialize();

        return $this->collection->findFirst($p);
    }

    #[Override]
    public function filter(Closure $p): Collection
    {
        $this->initialize();

        return $this->collection->filter($p);
    }

    #[Override]
    public function forAll(Closure $p): bool
    {
        $this->initialize();

        return $this->collection->forAll($p);
    }

    #[Override]
    public function map(Closure $func): Collection
    {
        $this->initialize();

        return $this->collection->map($func);
    }

    #[Override]
    public function reduce(Closure $func, mixed $initial = null): mixed
    {
        $this->initialize();

        return $this->collection->reduce($func, $initial);
    }

    #[Override]
    public function partition(Closure $p): array
    {
        $this->initialize();

        return $this->collection->partition($p);
    }

    /** @template TMaybeContained */
    #[Override]
    public function indexOf(mixed $element): string|int|false
    {
        $this->initialize();

        return $this->collection->indexOf($element);
    }

    #[Override]
    public function slice(int $offset, int|null $length = null): array
    {
        $this->initialize();

        return $this->collection->slice($offset, $length);
    }

    /**
     * @return Traversable<int|string, mixed>
     * @phpstan-return Traversable<TKey,T>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        $this->initialize();

        return $this->collection->getIterator();
    }

    /** @param TKey $offset */
    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        $this->initialize();

        return $this->collection->offsetExists($offset);
    }

    /** @param TKey $offset */
    #[Override]
    public function offsetGet(mixed $offset): mixed
    {
        $this->initialize();

        return $this->collection->offsetGet($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T         $value
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /** @param TKey $offset */
    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * Is the lazy collection already initialized?
     *
     * @phpstan-assert-if-true Collection<TKey,T> $this->collection
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Initialize the collection
     *
     * @phpstan-assert Collection<TKey,T> $this->collection
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

    #[Override]
    public function matching(Criteria $criteria): ReadableCollection
    {
        $this->initialize();

        return $this->collection->matching($criteria);
    }
}
