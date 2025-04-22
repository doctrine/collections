<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function array_merge;
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

    public function testMerge(): void
    {
        $a        = [1, 2, 3, 'sd' => 'a2222212'];
        $b        = ['aa' => 12, 'sd' => 'b2212'];
        $c        = ['cc' => 122, 'sd' => 'c3111212'];
        $expected = array_merge($a, $b);

        // Merge one collection
        $arrayCollectionA = new ArrayCollection($a);
        $arrayCollectionB = new ArrayCollection($b);
        $arrayCollectionC = new ArrayCollection($c);
        $merged           = $arrayCollectionA->merge($arrayCollectionB);
        $this->assertEquals($expected, $merged->toArray());

        // Merge two collections
        unset($arrayCollectionA);
        $arrayCollectionA = new ArrayCollection($a);
        $expected         = array_merge($a, $b, $c);
        $merged           = $arrayCollectionA->merge($arrayCollectionB, $arrayCollectionC);
        $this->assertEquals($expected, $merged->toArray());
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
