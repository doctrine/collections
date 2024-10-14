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
