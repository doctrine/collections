<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use ArrayAccess;
use Closure;
use Countable;
use IteratorAggregate;

/**
 * The missing (SPL) Collection/Array/OrderedMap interface.
 *
 * A Collection resembles the nature of a regular PHP array. That is,
 * it is essentially an <b>ordered map</b> that can also be used
 * like a list.
 *
 * A Collection has an internal iterator just like a PHP array. In addition,
 * a Collection can be iterated with external iterators, which is preferable.
 * To use an external iterator simply use the foreach language construct to
 * iterate over the collection (which calls {@link getIterator()} internally) or
 * explicitly retrieve an iterator though {@link getIterator()} which can then be
 * used to iterate over the collection.
 * You can not rely on the internal iterator of the collection being at a certain
 * position unless you explicitly positioned it before. Prefer iteration with
 * external iterators.
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends IteratorAggregate<TKey, T>
 * @template-extends ArrayAccess<TKey, T>
 *
 * @method array toList()
 */
interface Collection extends Countable, IteratorAggregate, ArrayAccess
{
    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     * @psalm-param T $element
     *
     * @return true Always TRUE.
     */
    public function add($element): bool;

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection.
     *
     * @param mixed $element The element to search for.
     * @psalm-param T $element
     *
     * @return bool TRUE if the collection contains the element, FALSE otherwise.
     */
    public function contains($element): bool;

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return bool TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty(): bool;

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|int $key The key/index of the element to remove.
     * @psalm-param TKey $key
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element.
     * @psalm-return T|null
     */
    public function remove($key);

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     * @psalm-param T $element
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element): bool;

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|int $key The key/index to check for.
     * @psalm-param TKey $key
     *
     * @return bool TRUE if the collection contains an element with the specified key/index,
     *              FALSE otherwise.
     */
    public function containsKey($key): bool;

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|int $key The key/index of the element to retrieve.
     * @psalm-param TKey $key
     *
     * @return mixed
     * @psalm-return T|null
     */
    public function get($key);

    /**
     * Gets all keys/indices of the collection.
     *
     * @return int[]|string[] The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection.
     * @psalm-return TKey[]
     */
    public function getKeys(): array;

    /**
     * Gets all values of the collection.
     *
     * @return mixed[] The values of all elements in the collection, in the
     *                 order they appear in the collection.
     * @psalm-return list<T>
     */
    public function getValues(): array;

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|int $key   The key/index of the element to set.
     * @param mixed      $value The element to set.
     * @psalm-param TKey $key
     * @psalm-param T $value
     *
     * @return void
     */
    public function set($key, $value): void;

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return mixed[]
     * @psalm-return array<TKey,T>
     */
    public function toArray(): array;

    ///**
    // * Gets a native PHP list representation of the collection.
    // *
    // * @psalm-return list<T>
    // */
    //public function toList(): array;

    /**
     * Sets the internal iterator to the first element in the collection and returns this element.
     *
     * @return mixed
     * @psalm-return T|false
     */
    public function first();

    /**
     * Sets the internal iterator to the last element in the collection and returns this element.
     *
     * @return mixed
     * @psalm-return T|false
     */
    public function last();

    /**
     * Gets the key/index of the element at the current iterator position.
     *
     * @return int|string|null
     * @psalm-return TKey|null
     */
    public function key();

    /**
     * Gets the element of the collection at the current iterator position.
     *
     * @return mixed
     * @psalm-return T|false
     */
    public function current();

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return mixed
     * @psalm-return T|false
     */
    public function next();

    /**
     * Tests for the existence of an element that satisfies the given predicate.
     *
     * @param Closure $p The predicate.
     * @psalm-param Closure(TKey=, T=):bool $p
     *
     * @return bool TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    public function exists(Closure $p): bool;

    /**
     * Returns the first element of this collection that satisfies the predicate p.
     *
     * @param Closure $p The predicate.
     *
     * @return mixed The first element respecting the predicate,
     *               null if no element respects the predicate.
     *
     * @psalm-param Closure(TKey=, T=):bool $p
     * @psalm-return T|null
     */
    public function findFirst(Closure $p);

    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure $p The predicate used for filtering.
     * @psalm-param Closure(T=):bool $p
     *
     * @return Collection<mixed> A collection with the results of the filter operation.
     * @psalm-return Collection<TKey, T>
     */
    public function filter(Closure $p): self;

    /**
     * Tests whether the given predicate p holds for all elements of this collection.
     *
     * @param Closure $p The predicate.
     * @psalm-param Closure(TKey=, T=):bool $p
     *
     * @return bool TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    public function forAll(Closure $p): bool;

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @psalm-param Closure(T=):U $func
     *
     * @return Collection<mixed>
     * @psalm-return Collection<TKey, U>
     *
     * @psalm-template U
     */
    public function map(Closure $func): self;

    /**
     * Applies iteratively the given function to each element in the collection,
     * so as to reduce the collection to a single value.
     *
     * @param mixed $initial
     *
     * @return mixed
     *
     * @psalm-template TReturn
     * @psalm-template TInitial
     * @psalm-param Closure(TReturn|TInitial|null, T):(TInitial|TReturn) $func
     * @psalm-param TInitial|null $initial
     * @psalm-return TReturn|TInitial|null
     */
    public function reduce(Closure $func, $initial = null);

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $p The predicate on which to partition.
     * @psalm-param Closure(TKey=, T=):bool $p
     *
     * @return Collection<mixed>[] An array with two elements. The first element contains the collection
     *                      of elements where the predicate returned TRUE, the second element
     *                      contains the collection of elements where the predicate returned FALSE.
     * @psalm-return array{0: Collection<TKey, T>, 1: Collection<TKey, T>}
     */
    public function partition(Closure $p): array;

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for.
     * @psalm-param T $element
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found.
     * @psalm-return TKey|false
     */
    public function indexOf($element);

    /**
     * Extracts a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on.
     *
     * @param int      $offset The offset to start from.
     * @param int|null $length The maximum number of elements to return, or null for no limit.
     *
     * @return mixed[]
     * @psalm-return array<TKey,T>
     */
    public function slice(int $offset, ?int $length = null): array;

    /**
     * {@inheritdoc}
     */
    public function count(): int;

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable;

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset);

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void;

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void;

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool;
}
