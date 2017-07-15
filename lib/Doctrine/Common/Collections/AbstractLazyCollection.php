<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Closure;

/**
 * Lazy collection that is backed by a concrete collection
 *
 * @author Michaël Gallego <mic.gallego@gmail.com>
 * @since  1.2
 */
abstract class AbstractLazyCollection implements Collection
{
    /**
     * The backed collection to use
     *
     * @var Collection
     */
    protected $collection;

    /**
     * @var boolean
     */
    protected $initialized = false;

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        $this->initialize();
        return $this->collection->count();
    }

    /**
     * {@inheritDoc}
     */
    public function add($element): bool
    {
        $this->initialize();
        return $this->collection->add($element);
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
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
    public function exists(Closure $p): bool
    {
        $this->initialize();
        return $this->collection->exists($p);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p): Collection
    {
        $this->initialize();
        return $this->collection->filter($p);
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p): bool
    {
        $this->initialize();
        return $this->collection->forAll($p);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func): Collection
    {
        $this->initialize();
        return $this->collection->map($func);
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
     */
    public function indexOf($element)
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
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        $this->initialize();
        return $this->collection->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset): bool
    {
        $this->initialize();
        return $this->collection->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        $this->initialize();
        return $this->collection->offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->initialize();
        $this->collection->offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset): void
    {
        $this->initialize();
        $this->collection->offsetUnset($offset);
    }

    /**
     * Is the lazy collection already initialized?
     *
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Initialize the collection
     *
     * @return void
     */
    protected function initialize(): void
    {
        if ( ! $this->initialized) {
            $this->doInitialize();
            $this->initialized = true;
        }
    }

    /**
     * Do the initialization logic
     *
     * @return void
     */
    abstract protected function doInitialize(): void;
}
