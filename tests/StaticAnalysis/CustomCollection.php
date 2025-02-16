<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\StaticAnalysis;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @phpstan-template TKey of array-key
 * @phpstan-template T of object
 * @phpstan-implements Collection<TKey, T>
 */
abstract class CustomCollection implements Collection
{
    /** @var ArrayCollection<TKey, T> */
    private ArrayCollection $collection;

    /** @param ArrayCollection<TKey, T> $arrayCollection */
    public function __construct(ArrayCollection $arrayCollection)
    {
        $this->collection = $arrayCollection;
    }

    /**
     * @phpstan-param Closure(T, TKey):bool $p
     *
     * @return Collection<TKey, T>
     */
    public function filter(Closure $p)
    {
        return $this->collection->filter($p);
    }

    /**
     * @phpstan-param Closure(TKey, T):bool $p
     *
     * @phpstan-return array{0: Collection<TKey, T>, 1: Collection<TKey, T>}
     */
    public function partition(Closure $p)
    {
        return $this->collection->partition($p);
    }
}
