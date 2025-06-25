<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function serialize;
use function unserialize;

/**
 * Tests for {@see \Doctrine\Common\Collections\ArrayCollection}.
 *
 * @covers \Doctrine\Common\Collections\ArrayCollection
 */
class ArrayCollectionTest extends ArrayCollectionTestCase
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<mixed>
     */
    protected function buildCollection(array $elements = []): Collection
    {
        return new ArrayCollection($elements);
    }

    public function testUnserializeEmptyArrayCollection(): void
    {
        $collection            = new SerializableArrayCollection();
        $serializeCollection   = serialize($collection);
        $unserializeCollection = unserialize($serializeCollection);

        $this->assertIsArray($unserializeCollection->getValues());
        $this->assertCount(0, $unserializeCollection->getValues());
    }

    public function testFlatMap(): void
    {
        $collection = new ArrayCollection([
            'a' => [1, 2, 3],
            'b' => [4, 5, 6],
            'c' => [7, 8, 9]
        ]);

        $result = $collection->flatMap(static fn ($item) => $item);
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->toArray());
    }

    public function testFlatMapWithEmptyArrays(): void
    {
        $collection = new ArrayCollection([
            'a' => [1, 2],
            'b' => [],
            'c' => [3, 4]
        ]);

        $result = $collection->flatMap(static fn ($item) => $item);
        $this->assertSame([1, 2, 3, 4], $result->toArray());
    }

    public function testFlatMapWithTransformation(): void
    {
        $collection = new ArrayCollection([1, 2, 3]);

        $result = $collection->flatMap(static fn ($item) => [$item, $item * 2]);
        $this->assertSame([1, 2, 2, 4, 3, 6], $result->toArray());
    }

    public function testFlatMapWithNonArrayReturns(): void
    {
        $collection = new ArrayCollection([1, 2, 3]);

        $result = $collection->flatMap(static fn ($item) => $item * 2);
        $this->assertSame([2, 4, 6], $result->toArray());
    }

    public function testFlatMapWithMixedReturns(): void
    {
        $collection = new ArrayCollection([1, 2, 3]);

        $result = $collection->flatMap(static function ($item) {
            if ($item % 2 === 0) {
                return [$item, $item * 2]; // Return array for even numbers
            }
            return $item * 3; // Return scalar for odd numbers
        });
        $this->assertSame([3, 2, 4, 9], $result->toArray());
    }

    public function testFlatMapWithNullReturns(): void
    {
        $collection = new ArrayCollection([1, 2, 3]);

        $result = $collection->flatMap(static fn ($item) => null);
        $this->assertSame([null, null, null], $result->toArray());
    }
}

/**
 * @template TKey of array-key
 * @template TValue
 * @extends ArrayCollection<TKey, TValue>
 */
class SerializableArrayCollection extends ArrayCollection
{
    /** @return array<TKey, TValue> */
    public function __serialize(): array
    {
        return $this->toArray();
    }

    /** @param array<TKey, TValue> $data */
    public function __unserialize(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
