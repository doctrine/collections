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

/**
 * An AbstractWrappedCollection is a Collection implementation that wraps another Collection.
 *
 * @since  1.3
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
abstract class AbstractWrappedCollection extends ArrayCollection
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?: new ArrayCollection();
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
    public function next()
    {
        return $this->collection->next();
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
    public function remove($key)
    {
        return $this->collection->remove($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        return $this->collection->removeElement($element);
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
    public function contains($element)
    {
        return $this->collection->contains($element);
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
    public function indexOf($element)
    {
        return $this->collection->indexOf($element);
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
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->collection->set($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        return $this->collection->add($value);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return $this->collection->isEmpty();
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        return new static($this->collection->map($func));
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static($this->collection->filter($p));
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
    public function partition(Closure $p)
    {
        $results = $this->collection->partition($p);

        return array(new static($results[0]), new static($results[1]));
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->collection->clear();
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
    public function matching(Criteria $criteria)
    {
        return $this->collection->matching($criteria);
    }

    /**
     * Retrieves the wrapped Collection instance.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function unwrap()
    {
        return $this->collection;
    }
}
