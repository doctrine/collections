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
     * @return ArrayCollection<mixed>
     */
    protected function buildCollection(array $elements = []): Collection
    {
        return new ArrayCollection($elements);
    }

    public function testUnserializeEmptyArrayCollection() : void
    {
        $collection            = new SerializableArrayCollection();
        $serializeCollection   = serialize($collection);
        $unserializeCollection = unserialize($serializeCollection);

        $this->assertIsArray($unserializeCollection->getValues());
        $this->assertCount(0, $unserializeCollection->getValues());
    }

    public function testToList(): void
    {
        $this->assertSame(['foo', 'bar'], $this->buildCollection(['foo', 'bar'])->toList());
        $this->assertSame(['foo', 'bar'], $this->buildCollection(['key1' => 'foo', 'key2' => 'bar'])->toList());
    }
}

/**
 * We can't implement Serializable interface on anonymous class
 */
class SerializableArrayCollection extends ArrayCollection implements Serializable
{
    public function serialize() : string
    {
        return json_encode($this->getKeys());
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
    public function unserialize($serialized) : void
    {
        foreach (json_decode($serialized) as $value) {
            parent::add($value);
        }
    }
}
