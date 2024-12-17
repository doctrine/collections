<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use ArrayAccess;
use Closure;

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
 * @phpstan-template TKey of array-key
 * @phpstan-template T
 * @template-extends ReadableCollection<TKey, T>
 * @template-extends ArrayAccess<TKey, T>
 */
interface Collection extends ReadableCollection, ArrayAccess
{
    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     * @phpstan-param T $element
     *
     * @return void we will require a native return type declaration in 3.0
     */
    public function add(mixed $element);

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    public function clear();

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|int $key The key/index of the element to remove.
     * @phpstan-param TKey $key
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element.
     * @phpstan-return T|null
     */
    public function remove(string|int $key);

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     * @phpstan-param T $element
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement(mixed $element);

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|int $key   The key/index of the element to set.
     * @param mixed      $value The element to set.
     * @phpstan-param TKey $key
     * @phpstan-param T $value
     *
     * @return void
     */
    public function set(string|int $key, mixed $value);

    /**
     * {@inheritDoc}
     *
     * @phpstan-param Closure(T):U $func
     *
     * @return Collection<mixed>
     * @phpstan-return Collection<TKey, U>
     *
     * @phpstan-template U
     */
    public function map(Closure $func);

    /**
     * {@inheritDoc}
     *
     * @phpstan-param Closure(T, TKey):bool $p
     *
     * @return Collection<mixed> A collection with the results of the filter operation.
     * @phpstan-return Collection<TKey, T>
     */
    public function filter(Closure $p);

    /**
     * {@inheritDoc}
     *
     * @phpstan-param Closure(TKey, T):bool $p
     *
     * @return Collection<mixed>[] An array with two elements. The first element contains the collection
     *                      of elements where the predicate returned TRUE, the second element
     *                      contains the collection of elements where the predicate returned FALSE.
     * @phpstan-return array{0: Collection<TKey, T>, 1: Collection<TKey, T>}
     */
    public function partition(Closure $p);
}
