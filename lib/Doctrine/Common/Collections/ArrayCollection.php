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

use Closure, ArrayIterator;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;

/**
 * An ArrayCollection is a Collection implementation that wraps a regular PHP array.
 *
 * @since  2.0
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Jonathan Wage <jonwage@gmail.com>
 * @author Roman Borschel <roman@code-factory.org>
 */
class ArrayCollection implements Collection, Selectable
{
    /**
     * An array containing the entries of this collection.
     *
     * @var array
     */
    private $_elements;

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        $this->_elements = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->_elements;
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        reset($this->_elements);
        return $this->get(key($this->_elements));
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        end($this->_elements);
        return $this->get(key($this->_elements));
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        next($this->_elements);
        return $this->get(key($this->_elements));
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->get(key($this->_elements));
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if ($this->containsKey($key)) {
            $removed = $this->get($key);
            unset($this->_elements[$key]);

            return $removed;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        $key = $this->indexOf($element);

        if ($key !== false) {
            unset($this->_elements[$key]);

            return true;
        }

        return false;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if ( ! isset($offset)) {
            return $this->add($value);
        }
        return $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return isset($this->_elements[$key]) || array_key_exists($key, $this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return in_array($element, $this->toArray(), true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        foreach ($this->_elements as $key => $element) {
            if ($p($key, $this->get($key))) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return array_search($element, $this->toArray(), true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if (isset($this->_elements[$key])) {
            return $this->_elements[$key];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->_elements[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->_elements[] = $value;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return ! $this->_elements;
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        return new static(array_map($func, $this->toArray()));
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static(array_filter($this->toArray(), $p));
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p)
    {
        foreach ($this->_elements as $key => $element) {
            if ( ! $p($key, $this->get($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        $coll1 = $coll2 = array();
        foreach ($this->toArray() as $key => $element) {
            if ($p($key, $element)) {
                $coll1[$key] = $element;
            } else {
                $coll2[$key] = $element;
            }
        }
        return array(new static($coll1), new static($coll2));
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->_elements = array();
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        return array_slice($this->toArray(), $offset, $length, true);
    }

    /**
     * {@inheritDoc}
     */
    public function matching(Criteria $criteria)
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = $this->toArray();

        if ($expr) {
            $visitor  = new ClosureExpressionVisitor();
            $filter   = $visitor->dispatch($expr);
            $filtered = array_filter($filtered, $filter);
        }

        if ($orderings = $criteria->getOrderings()) {
            $next = null;
            foreach (array_reverse($orderings) as $field => $ordering) {
                $next = ClosureExpressionVisitor::sortByField($field, $ordering == 'DESC' ? -1 : 1, $next);
            }

            usort($filtered, $next);
        }

        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();

        if ($offset || $length) {
            $filtered = array_slice($filtered, (int)$offset, $length);
        }

        return new static($filtered);
    }
}
