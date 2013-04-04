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

use ArrayIterator;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

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
     * @var Symfony\Component\PropertyAccess\PropertyAccess
     */
    private $_property_accessor;

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
        return reset($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->_elements);
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
        return next($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if (isset($this->_elements[$key]) || array_key_exists($key, $this->_elements)) {
            $removed = $this->_elements[$key];
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
        $key = array_search($element, $this->_elements, true);

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
        return in_array($element, $this->_elements, true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists($p)
    {
        $p = $this->createClosure($p);

        foreach ($this->_elements as $key => $element) {
            if ($p($key, $element)) {
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
        return array_search($element, $this->_elements, true);
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
        return array_keys($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->_elements);
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
        return new ArrayIterator($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function map($p)
    {
        $p = $this->createClosure($p);

        return new static(array_map($p, $this->_elements));
    }

    /**
     * {@inheritDoc}
     */
    public function filter($p)
    {
        $p = $this->createClosure($p);

        return new static(array_filter($this->_elements, $p));
    }

    /**
     * {@inheritDoc}
     */
    public function forAll($p)
    {
        $p = $this->createClosure($p);

        foreach ($this->_elements as $key => $element) {
            if ( ! $p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function partition($p)
    {
        $p = $this->createClosure($p);
        $coll1 = $coll2 = array();

        foreach ($this->_elements as $key => $element) {
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
        return array_slice($this->_elements, $offset, $length, true);
    }

    /**
     * {@inheritDoc}
     */
    public function matching(Criteria $criteria)
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = $this->_elements;

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

    /**
     * Converts a predicate to closure, if the predicate is a string.
     *
     * @see \Symfony\Component\PropertyAccess\PropertyAccessorInterface::getValue()
     *
     * @param       string|callable     $p      The predicate. Can be callable, method name or PropertyPath string.
     *
     * @return      \Closure
     *
     * @throws      \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException      Type of the predicate is unexpected
     */
    protected function createClosure($p)
    {
        if (is_callable($p)) {
            return $p;
        }

        if (!is_string($p)) {
            throw new UnexpectedTypeException($p, 'string');
        }

        $that = $this;

        return function() use ($p, $that) {
            // only item received
            if (func_num_args() == 1) {
                $key  = null;
                $item = func_get_arg(0);
            }
            // key and item received
            else {
                list($key, $item) = func_get_args();
            }

            // explicit method call
            if (is_callable($closure = array($item, $p))) {
                return $closure($key);
            }
            // property getter, isser or hasser call
            else {
                return $that->getPropertyAccessor()
                            ->getValue($item, $p);
            }
        };
    }

    /**
     * Returns an instance of PropertyAccessor.
     *
     * @return Symfony\Component\PropertyAccess\PropertyAccess
     */
    private function getPropertyAccessor()
    {
        if (!is_object($this->_property_accessor))
        {
            $this->_property_accessor = PropertyAccess::getPropertyAccessor();
        }

        return $this->_property_accessor;
    }
}
