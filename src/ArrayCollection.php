<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use ArrayIterator;
use Closure;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Stringable;
use Traversable;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_reduce;
use function array_reverse;
use function array_search;
use function array_slice;
use function array_values;
use function count;
use function current;
use function end;
use function in_array;
use function key;
use function next;
use function reset;
use function spl_object_hash;
use function uasort;

use const ARRAY_FILTER_USE_BOTH;

/**
 * An ArrayCollection is a Collection implementation that wraps a regular PHP array.
 *
 * Warning: Using (un-)serialize() on a collection is not a supported use-case
 * and may break when we change the internals in the future. If you need to
 * serialize a collection use {@link toArray()} and reconstruct the collection
 * manually.
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-implements Collection<TKey,T>
 * @template-implements Selectable<TKey,T>
 * @psalm-consistent-constructor
 */
class ArrayCollection implements Collection, Selectable, Stringable
{
    /**
     * An array containing the entries of this collection.
     *
     * @psalm-var array<TKey,T>
     * @var mixed[]
     */
    private array $elements = [];

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     * @psalm-param array<TKey,T> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function first(): mixed
    {
        return reset($this->elements);
    }

    /**
     * Creates a new instance from the specified elements.
     *
     * This method is provided for derived classes to specify how a new
     * instance should be created when constructor semantics have changed.
     *
     * @param array $elements Elements.
     * @psalm-param array<K,V> $elements
     *
     * @psalm-return static<K,V>
     *
     * @psalm-template K of array-key
     * @psalm-template V
     */
    protected function createFrom(array $elements): static
    {
        return new static($elements);
    }

    public function last(): mixed
    {
        return end($this->elements);
    }

    public function key(): int|string|null
    {
        return key($this->elements);
    }

    public function next(): mixed
    {
        return next($this->elements);
    }

    public function current(): mixed
    {
        return current($this->elements);
    }

    public function remove(string|int $key): mixed
    {
        if (! isset($this->elements[$key]) && ! array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    public function removeElement(mixed $element): bool
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param TKey $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param TKey|null $offset
     * @param T         $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param TKey $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function containsKey(string|int $key): bool
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @template TMaybeContained
     */
    public function contains(mixed $element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function exists(Closure $p): bool
    {
        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-param TMaybeContained $element
     *
     * @psalm-return (TMaybeContained is T ? TKey|false : false)
     *
     * @template TMaybeContained
     */
    public function indexOf($element): int|string|false
    {
        return array_search($element, $this->elements, true);
    }

    public function get(string|int $key): mixed
    {
        return $this->elements[$key] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues(): array
    {
        return array_values($this->elements);
    }

    /** @return int<0, max> */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value): void
    {
        $this->elements[$key] = $value;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     *
     * This breaks assumptions about the template type, but it would
     * be a backwards-incompatible change to remove this method
     */
    public function add(mixed $element): void
    {
        $this->elements[] = $element;
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @return Traversable<int|string, mixed>
     * @psalm-return Traversable<TKey, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-param Closure(T):U $func
     *
     * @return static
     * @psalm-return static<TKey, U>
     *
     * @psalm-template U
     */
    public function map(Closure $func): Collection
    {
        return $this->createFrom(array_map($func, $this->elements));
    }

    /**
     * {@inheritDoc}
     */
    public function reduce(Closure $func, $initial = null): mixed
    {
        return array_reduce($this->elements, $func, $initial);
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     * @psalm-return static<TKey,T>
     */
    public function filter(Closure $p): Collection
    {
        return $this->createFrom(array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    public function findFirst(Closure $p): mixed
    {
        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                return $element;
            }
        }

        return null;
    }

    public function forAll(Closure $p): bool
    {
        foreach ($this->elements as $key => $element) {
            if (! $p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p): array
    {
        $matches = $noMatches = [];

        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }

        return [$this->createFrom($matches), $this->createFrom($noMatches)];
    }

    /**
     * Returns a string representation of this object.
     */
    public function __toString(): string
    {
        return self::class . '@' . spl_object_hash($this);
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $offset, int|null $length = null): array
    {
        return array_slice($this->elements, $offset, $length, true);
    }

    /** @psalm-return Collection<TKey, T>&Selectable<TKey,T> */
    public function matching(Criteria $criteria): Collection
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = $this->elements;

        if ($expr) {
            $visitor  = new ClosureExpressionVisitor();
            $filter   = $visitor->dispatch($expr);
            $filtered = array_filter($filtered, $filter);
        }

        $orderings = $criteria->getOrderings();

        if ($orderings) {
            $next = null;
            foreach (array_reverse($orderings) as $field => $ordering) {
                $next = ClosureExpressionVisitor::sortByField($field, $ordering === Criteria::DESC ? -1 : 1, $next);
            }

            uasort($filtered, $next);
        }

        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();

        if ($offset || $length) {
            $filtered = array_slice($filtered, (int) $offset, $length, true);
        }

        return $this->createFrom($filtered);
    }
}
