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
    protected function buildCollection(array $elements = []): Collection
    {
        return new ArrayCollection($elements);
    }

    /**
     * @return array
     */
    public function provideDifferentElementsForSorting(): array
    {
        $obj = new \stdClass();
        $obj->id = 2;
        $obj2 = new \stdClass();
        $obj2->id = 42;
        $obj3 = new \stdClass();
        $obj3->id = 1;

        return [
            [
                [1, 2, 3, 4, 5],
                function ($a, $b) {
                    return $a > $b ? -1 : 1;
                },
                [4 => 5, 3 => 4, 2 => 3, 1 => 2, 0 => 1],
            ],
            [
                ['A' => 'a', 'B' => 'b', 'C' => 'c'],
                function ($a, $b) {
                    return $a > $b ? -1 : 1;
                },
                ['C' => 'c', 'B' => 'b', 'A' => 'a'],
            ],
            [
                [$obj, $obj2, $obj3],
                function ($a, $b) {
                    return $a->id > $b->id ? 1 : -1;
                },
                [2 => $obj3, 0 => $obj, 1 => $obj2],
            ],
        ];
    }

    /**
     * @dataProvider provideDifferentElementsForSorting
     * @param array $elements
     * @param callable $closure
     * @param array $expectedElements
     */
    public function testSortWith(array $elements, callable $closure, array $expectedElements)
    {
        $collection = $this->buildCollection($elements);
        $sortedCollection = $collection->sortWith($closure);

        self::assertSame($elements, $collection->toArray(), "The original collection should not have changed");
        self::assertSame($expectedElements, $sortedCollection->toArray(), "The collection is not sorted as expected.");
    }
}
