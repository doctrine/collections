<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

use function serialize;
use function unserialize;

/**
 * Tests for {@see ArrayCollection}.
 */
#[CoversClass(ArrayCollection::class)]
class ArrayCollectionTest extends ArrayCollectionTestCase
{
    #[Override]
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
