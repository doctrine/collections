<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Serializable;

use function json_decode;
use function json_encode;
use function serialize;
use function unserialize;

use const JSON_THROW_ON_ERROR;

/**
 * Tests for {@see \Doctrine\Common\Collections\ArrayCollection}.
 *
 * @covers \Doctrine\Common\Collections\ArrayCollection
 */
class ArrayCollectionTest extends BaseArrayCollectionTest
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
 * We can't implement Serializable interface on anonymous class
 */
class SerializableArrayCollection extends ArrayCollection implements Serializable
{
    public function serialize(): string
    {
        return json_encode($this->getKeys(), JSON_THROW_ON_ERROR);
    }

    public function unserialize(string $serialized): void
    {
        foreach (json_decode(json: $serialized, flags: JSON_THROW_ON_ERROR) as $value) {
            parent::add($value);
        }
    }
}
