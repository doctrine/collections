<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Closure;
use Countable;
use IteratorAggregate;

/**
 * @psalm-template TKey of array-key
 * @template-covariant T
 * @template-extends IteratorAggregate<TKey, T>
 */
interface ReadableCollection extends Countable, IteratorAggregate
{
    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection.
     *
     * @param mixed $element The element to search for.
     * @psalm-param TMaybeContained $element
     *
     * @return bool TRUE if the collection contains the element, FALSE otherwise.
     * @psalm-return (TMaybeContained is T ? bool : false)
     *
     * @template TMaybeContained
     */
    public function contains(mixed $element): bool;

    /**
     * Checks whether the collection is empty (contains no elements).
     *
     * @return bool TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty(): bool;

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|int $key The key/index to check for.
     * @psalm-param TKey $key
     *
     * @return bool TRUE if the collection contains an element with the specified key/index,
     *              FALSE otherwise.
     */
    public function containsKey(string|int $key): bool;

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|int $key The key/index of the element to retrieve.
     * @psalm-param TKey $key
     *
     * @psalm-return T|null
     */
    public function get(string|int $key): mixed;

    /**
     * Gets all keys/indices of the collection.
     *
     * @return int[]|string[] The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection.
     * @psalm-return list<TKey>
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
     * Gets a native PHP array representation of the collection.
     *
     * @return mixed[]
     * @psalm-return array<TKey,T>
     */
    public function toArray(): array;

    /**
     * Sets the internal iterator to the first element in the collection and returns this element.
     *
     * @psalm-return T|false
     */
    public function first(): mixed;

    /**
     * Sets the internal iterator to the last element in the collection and returns this element.
     *
     * @psalm-return T|false
     */
    public function last(): mixed;

    /**
     * Gets the key/index of the element at the current iterator position.
     *
     * @psalm-return TKey|null
     */
    public function key(): int|string|null;

    /**
     * Gets the element of the collection at the current iterator position.
     *
     * @psalm-return T|false
     */
    public function current(): mixed;

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @psalm-return T|false
     */
    public function next(): mixed;

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
    public function slice(int $offset, int|null $length = null): array;

    /**
     * Tests for the existence of an element that satisfies the given predicate.
     *
     * @param Closure $p The predicate.
     * @psalm-param Closure(TKey, T):bool $p
     *
     * @return bool TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    public function exists(Closure $p): bool;

    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure $p The predicate used for filtering.
     * @psalm-param Closure(T, TKey):bool $p
     *
     * @return ReadableCollection<mixed> A collection with the results of the filter operation.
     * @psalm-return ReadableCollection<TKey, T>
     */
    public function filter(Closure $p): self;

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @psalm-param Closure(T):U $func
     *
     * @return ReadableCollection<mixed>
     * @psalm-return ReadableCollection<TKey, U>
     *
     * @psalm-template U
     */
    public function map(Closure $func): self;

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $p The predicate on which to partition.
     * @psalm-param Closure(TKey, T):bool $p
     *
     * @return ReadableCollection<mixed>[] An array with two elements. The first element contains the collection
     *                      of elements where the predicate returned TRUE, the second element
     *                      contains the collection of elements where the predicate returned FALSE.
     * @psalm-return array{0: ReadableCollection<TKey, T>, 1: ReadableCollection<TKey, T>}
     */
    public function partition(Closure $p): array;

    /**
     * Tests whether the given predicate p holds for all elements of this collection.
     *
     * @param Closure $p The predicate.
     * @psalm-param Closure(TKey, T):bool $p
     *
     * @return bool TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    public function forAll(Closure $p): bool;

    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for.
     * @psalm-param TMaybeContained $element
     *
     * @return int|string|bool The key/index of the element or FALSE if the element was not found.
     * @psalm-return (TMaybeContained is T ? TKey|false : false)
     *
     * @template TMaybeContained
     */
    public function indexOf(mixed $element): int|string|false;

    /**
     * Returns the first element of this collection that satisfies the predicate p.
     *
     * @param Closure $p The predicate.
     * @psalm-param Closure(TKey, T):bool $p
     *
     * @return mixed The first element respecting the predicate,
     *               null if no element respects the predicate.
     * @psalm-return T|null
     */
    public function findFirst(Closure $p): mixed;

    /**
     * Applies iteratively the given function to each element in the collection,
     * so as to reduce the collection to a single value.
     *
     * @psalm-param Closure(TReturn|TInitial, T):TReturn $func
     * @psalm-param TInitial $initial
     *
     * @psalm-return TReturn|TInitial
     *
     * @psalm-template TReturn
     * @psalm-template TInitial
     */
    public function reduce(Closure $func, mixed $initial = null): mixed;
}
