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

namespace Doctrine\Common\Collections;

use Closure;
use Traversable;
use BadMethodCallException;

/**
 * Unmodifiable view of a collection.
 *
 * Wraps original collection and protects it from unplanned change.
 * 
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class UnmodifiableCollectionView implements Collection
{
    /**
     * @var Collection
     */
    private $collection;


    /**
     * Initializes a new CollectionView.
     *
     * @param Collection $collection Original collection.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function add($element)
    {
        throw new BadMethodCallException('Cannot add to an unmodifiable collection');
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function clear()
    {
        throw new BadMethodCallException('Cannot clear an unmodifiable collection');
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return $this->collection->contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return $this->collection->isEmpty();
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function remove($key)
    {
        throw new BadMethodCallException('Cannot remove from an unmodifiable collection');
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function removeElement($element)
    {
        throw new BadMethodCallException('Cannot remove from an unmodifiable collection');
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return $this->collection->containsKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->collection->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return $this->collection->getKeys();
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return $this->collection->getValues();
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function set($key, $value)
    {
        throw new BadMethodCallException('Cannot set on an unmodifiable collection');
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->collection->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return $this->collection->first();
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return $this->collection->last();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->collection->key();
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->collection->current();
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return $this->collection->next();
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        return $this->collection->exists($p);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return $this->collection->filter($p);
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p)
    {
        return $this->collection->forAll($p);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        return $this->collection->map($func);
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        return $this->collection->partition($p);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return $this->collection->indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        return $this->collection->slice($offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->collection->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->collection->offsetGet($offset);
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('Cannot set on an unmodifiable collection');
    }

    /**
     * This operation is not supported by an unmodifiable collection.
     *
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Cannot unset from an unmodifiable collection');
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->collection->count();
    }
}