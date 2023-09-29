<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Closure;
use LogicException;
use ReturnTypeWillChange;
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

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        $this->initialize();

        return $this->collection->count();
    }

    /**
     * {@inheritDoc}
     */
    public function add(mixed $element)
    {
        $this->initialize();

        $this->collection->add($element);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->initialize();
        $this->collection->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function contains(mixed $element)
    {
        $this->initialize();

        return $this->collection->contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        $this->initialize();

        return $this->collection->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string|int $key)
    {
        $this->initialize();

        return $this->collection->remove($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement(mixed $element)
    {
        $this->initialize();

        return $this->collection->removeElement($element);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey(string|int $key)
    {
        $this->initialize();

        return $this->collection->containsKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string|int $key)
    {
        $this->initialize();

        return $this->collection->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        $this->initialize();

        return $this->collection->getKeys();
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        $this->initialize();

        return $this->collection->getValues();
    }

    /**
     * {@inheritDoc}
     */
    public function set(string|int $key, mixed $value)
    {
        $this->initialize();
        $this->collection->set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $this->initialize();

        return $this->collection->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        $this->initialize();

        return $this->collection->first();
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        $this->initialize();

        return $this->collection->last();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        $this->initialize();

        return $this->collection->key();
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $this->initialize();

        return $this->collection->current();
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->initialize();

        return $this->collection->next();
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        $this->initialize();

        return $this->collection->exists($p);
    }

    /**
     * {@inheritDoc}
     */
    public function findFirst(Closure $p)
    {
        $this->initialize();

        return $this->collection->findFirst($p);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        $this->initialize();

        return $this->collection->filter($p);
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p)
    {
        $this->initialize();

        return $this->collection->forAll($p);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        $this->initialize();

        return $this->collection->map($func);
    }

    /**
     * {@inheritDoc}
     */
    public function reduce(Closure $func, mixed $initial = null)
    {
        $this->initialize();

        return $this->collection->reduce($func, $initial);
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        $this->initialize();

        return $this->collection->partition($p);
    }

    /**
     * {@inheritDoc}
     *
     * @template TMaybeContained
     */
    public function indexOf(mixed $element)
    {
        $this->initialize();

        return $this->collection->indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $offset, int|null $length = null)
    {
        $this->initialize();

        return $this->collection->slice($offset, $length);
    }

    /**
     * {@inheritDoc}
     *
     * @return Traversable<int|string, mixed>
     * @psalm-return Traversable<TKey,T>
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $this->initialize();

        return $this->collection->getIterator();
    }

    /**
     * {@inheritDoc}
     *
     * @param TKey $offset
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function offsetExists(mixed $offset)
    {
        $this->initialize();

        return $this->collection->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @param TKey $offset
     *
     * @return T|null
     */
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
    {
        $this->initialize();

        return $this->collection->offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @param TKey|null $offset
     * @param T         $value
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /**
     * @param TKey $offset
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetUnset(mixed $offset)
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * Is the lazy collection already initialized?
     *
     * @return bool
     *
     * @psalm-assert-if-true Collection<TKey,T> $this->collection
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Initialize the collection
     *
     * @return void
     *
     * @psalm-assert Collection<TKey,T> $this->collection
     */
    protected function initialize()
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
     *
     * @return void
     */
    abstract protected function doInitialize();
}
