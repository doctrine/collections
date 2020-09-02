<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Tests for {@see \Doctrine\Common\Collections\ArrayCollection}.
 *
 * @covers \Doctrine\Common\Collections\ArrayCollection
 */
class ArrayCollectionTest extends BaseArrayCollectionTest
{
    /**
     * @param array $elements
     *
     * @return Collection|ArrayCollection
     */
    protected function buildCollection(array $elements = []) : Collection
    {
        return new ArrayCollection($elements);
    }

    public function testRebuildKeys()
    {
        $collection = $this->buildCollection([
            ['foo' => 1, 'bar' => 2],
            ['foo' => 2, 'bar' => 4],
            ['foo' => 2, 'bar' => 3],
        ]);

        $collection->remove(1);

        $this->assertEquals([
            ['foo' => 1, 'bar' => 2],
            ['foo' => 2, 'bar' => 3],
        ], $collection->rebuildKeys()->toArray());
    }
}
