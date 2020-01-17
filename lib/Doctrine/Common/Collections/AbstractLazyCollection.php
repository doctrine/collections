<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Closure;
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
     * @psalm-var Collection<TKey,T>
     * @var Collection
     */
    protected $collection;

    /** @var bool */
    protected $initialized = false;

    /**
     * {@inheritDoc}
     */
    public function count() : int
    {
        $this->initialize();

        return $this->collection->count();
    }

    /**
     * {@inheritDoc}
     */
    public function add($element) : bool
    {
        $this->initialize();

        return $this->collection->add($element);
    }

    /**
     * {@inheritDoc}
     */
    public function clear() : void
    {
        $this->initialize();
        $this->collection->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element) : bool
    {
        $this->initialize();

        return $this->collection->contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty() : bool
    {
        $this->initialize();

        return $this->collection->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $this->initialize();

        return $this->collection->remove($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element) : bool
    {
        $this->initialize();

        return $this->collection->removeElement($element);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key) : bool
    {
        $this->initialize();

        return $this->collection->containsKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        $this->initialize();

        return $this->collection->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys() : array
    {
        $this->initialize();

        return $this->collection->getKeys();
    }

    /**
     * {@inheritDoc}
     */
    public function getValues() : array
    {
        $this->initialize();

        return $this->collection->getValues();
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value) : void
    {
        $this->initialize();
        $this->collection->set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray() : array
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
    public function exists(Closure $p) : bool
    {
        $this->initialize();

        return $this->collection->exists($p);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p) : Collection
    {
        $this->initialize();

        return $this->collection->filter($p);
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p) : bool
    {
        $this->initialize();

        return $this->collection->forAll($p);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func) : Collection
    {
        $this->initialize();

        return $this->collection->map($func);
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p) : array
    {
        $this->initialize();

        return $this->collection->partition($p);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        $this->initialize();

        return $this->collection->indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $offset, ?int $length = null) : array
    {
        $this->initialize();

        return $this->collection->slice($offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator() : Traversable
    {
        $this->initialize();

        return $this->collection->getIterator();
    }

    /**
     * {@inheritDoc}
     *
     * @param int|string $offset
     */
    public function offsetExists($offset) : bool
    {
        $this->initialize();

        return $this->collection->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $this->initialize();

        return $this->collection->offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @param int|string $offset
     * @param mixed      $value
     */
    public function offsetSet($offset, $value) : void
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     *
     * @param int|string $offset
     */
    public function offsetUnset($offset) : void
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * Is the lazy collection already initialized?
     */
    public function isInitialized() : bool
    {
        return $this->initialized;
    }

    /**
     * Initialize the collection
     */
    protected function initialize() : void
    {
        if ($this->initialized) {
            return;
        }

        $this->doInitialize();
        $this->initialized = true;
    }

    /**
     * Do the initialization logic
     */
    abstract protected function doInitialize() : void;
}
