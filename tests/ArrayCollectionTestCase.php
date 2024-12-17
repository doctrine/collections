<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Selectable;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_keys;
use function array_search;
use function array_values;
use function count;
use function current;
use function end;
use function key;
use function next;
use function reset;

abstract class ArrayCollectionTestCase extends TestCase
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<mixed>
     */
    abstract protected function buildCollection(array $elements = []): Collection;

    protected function isSelectable(object $obj): bool
    {
        return $obj instanceof Selectable;
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testToArray(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame($elements, $collection->toArray());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testFirst(array $elements): void
    {
        $collection = $this->buildCollection($elements);
        self::assertSame(reset($elements), $collection->first());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testLast(array $elements): void
    {
        $collection = $this->buildCollection($elements);
        self::assertSame(end($elements), $collection->last());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testKey(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(key($elements), $collection->key());

        next($elements);
        $collection->next();

        self::assertSame(key($elements), $collection->key());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testNext(array $elements): void
    {
        $count      = count($elements);
        $collection = $this->buildCollection($elements);

        for ($i = 0; $i < $count; $i++) {
            $collectionNext = $collection->next();
            $arrayNext      = next($elements);

            if (! $collectionNext || ! $arrayNext) {
                break;
            }

            self::assertSame($arrayNext, $collectionNext, 'Returned value of ArrayCollection::next() and next() not match');
            self::assertSame(key($elements), $collection->key(), 'Keys not match');
            self::assertSame(current($elements), $collection->current(), 'Current values not match');
        }

        self::assertFalse($collection->next());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testCurrent(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(current($elements), $collection->current());

        next($elements);
        $collection->next();

        self::assertSame(current($elements), $collection->current());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testGetKeys(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(array_keys($elements), $collection->getKeys());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testGetValues(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(array_values($elements), $collection->getValues());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testCount(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(count($elements), $collection->count());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testIterator(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        $iterations = 0;
        foreach ($collection->getIterator() as $key => $item) {
            self::assertSame($elements[$key], $item, 'Item ' . $key . ' not match');
            ++$iterations;
        }

        self::assertEquals(count($elements), $iterations, 'Number of iterations not match');
    }

    /** @phpstan-return array<string, array{mixed[]}> */
    public static function provideDifferentElements(): array
    {
        return [
            'indexed'     => [[1, 2, 3, 4, 5]],
            'associative' => [['A' => 'a', 'B' => 'b', 'C' => 'c']],
            'mixed'       => [['A' => 'a', 1, 'B' => 'b', 2, 3]],
        ];
    }

    public function testRemove(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'B' => 'b', 3];
        $collection = $this->buildCollection($elements);

        self::assertEquals(1, $collection->remove(0));
        unset($elements[0]);

        self::assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);

        self::assertEquals(2, $collection->remove(1));
        unset($elements[1]);

        self::assertEquals('a', $collection->remove('A'));
        unset($elements['A']);

        self::assertEquals($elements, $collection->toArray());
    }

    public function testRemoveElement(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'B' => 'b', 3, 'A2' => 'a', 'B2' => 'b'];
        $collection = $this->buildCollection($elements);

        self::assertTrue($collection->removeElement(1));
        unset($elements[0]);

        self::assertFalse($collection->removeElement('non-existent'));

        self::assertTrue($collection->removeElement('a'));
        unset($elements['A']);

        self::assertTrue($collection->removeElement('a'));
        unset($elements['A2']);

        self::assertEquals($elements, $collection->toArray());
    }

    public function testContainsKey(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'B2' => 'b'];
        $collection = $this->buildCollection($elements);

        self::assertTrue($collection->containsKey(0), 'Contains index 0');
        self::assertTrue($collection->containsKey('A'), 'Contains key "A"');
        self::assertTrue($collection->containsKey('null'), 'Contains key "null", with value null');
        self::assertFalse($collection->containsKey('non-existent'), "Doesn't contain key");
    }

    public function testEmpty(): void
    {
        $collection = $this->buildCollection();
        self::assertTrue($collection->isEmpty(), 'Empty collection');

        $collection->add(1);
        self::assertFalse($collection->isEmpty(), 'Not empty collection');
    }

    public function testContains(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0];
        $collection = $this->buildCollection($elements);

        self::assertTrue($collection->contains(0), 'Contains Zero');
        self::assertTrue($collection->contains('a'), 'Contains "a"');
        self::assertTrue($collection->contains(null), 'Contains Null');
        self::assertFalse($collection->contains('non-existent'), "Doesn't contain an element");
    }

    public function testExists(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0];
        $collection = $this->buildCollection($elements);

        self::assertTrue($collection->exists(static fn ($key, $element) => $key === 'A' && $element === 'a'), 'Element exists');

        self::assertFalse($collection->exists(static fn ($key, $element) => $key === 'non-existent' && $element === 'non-existent'), 'Element not exists');
    }

    public function testFindFirst(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0];
        $collection = $this->buildCollection($elements);

        self::assertSame('a', $collection->findFirst(static fn ($key, $element) => $key === 'A' && $element === 'a'), 'Element exists');
    }

    public function testFindFirstNotFound(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0];
        $collection = $this->buildCollection($elements);

        self::assertNull($collection->findFirst(static fn ($key, $element) => $key === 'non-existent' && $element === 'non-existent'), 'Element does not exists');
    }

    public function testIndexOf(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0];
        $collection = $this->buildCollection($elements);

        self::assertSame(array_search(2, $elements, true), $collection->indexOf(2), 'Index of 2');
        self::assertSame(array_search(null, $elements, true), $collection->indexOf(null), 'Index of null');
        self::assertSame(array_search('non-existent', $elements, true), $collection->indexOf('non-existent'), 'Index of non existent');
    }

    public function testGet(): void
    {
        $elements   = [1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0];
        $collection = $this->buildCollection($elements);

        self::assertSame(2, $collection->get(1), 'Get element by index');
        self::assertSame('a', $collection->get('A'), 'Get element by name');
        self::assertSame(null, $collection->get('non-existent'), 'Get non existent element');
    }

    public function testMatchingWithSortingPreserveKeys(): void
    {
        $object1 = new stdClass();
        $object2 = new stdClass();

        $object1->sortField = 2;
        $object2->sortField = 1;

        $collection = $this->buildCollection([
            'object1' => $object1,
            'object2' => $object2,
        ]);

        if (! $this->isSelectable($collection)) {
            $this->markTestSkipped('Collection does not support Selectable interface');
        }

        self::assertSame(
            [
                'object2' => $object2,
                'object1' => $object1,
            ],
            $collection
                ->matching(new Criteria(null, ['sortField' => Criteria::ASC]))
                ->toArray(),
        );
        self::assertSame(
            [
                'object2' => $object2,
                'object1' => $object1,
            ],
            $collection
                ->matching(new Criteria(null, ['sortField' => Order::Ascending]))
                ->toArray(),
        );
    }

    /**
     * @param int[] $array
     * @param int[] $slicedArray
     *
     * @dataProvider provideSlices
     */
    public function testMatchingWithSlicingPreserveKeys(array $array, array $slicedArray, int|null $firstResult, int|null $maxResult): void
    {
        $collection = $this->buildCollection($array);

        if (! $this->isSelectable($collection)) {
            $this->markTestSkipped('Collection does not support Selectable interface');
        }

        self::assertSame(
            $slicedArray,
            $collection
                ->matching(new Criteria(null, null, $firstResult, $maxResult))
                ->toArray(),
        );
    }

    /** @return mixed[][] */
    public static function provideSlices(): array
    {
        return [
            'preserve numeric keys' => [
                [
                    0 => 1,
                    1 => 2,
                    2 => 3,
                    3 => 4,
                ],
                [
                    1 => 2,
                    2 => 3,
                ],
                1,
                2,
            ],
            'preserve string keys' => [
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                ],
                [
                    'b' => 2,
                    'c' => 3,
                ],
                1,
                2,
            ],
            'preserve keys on firstresult only' => [
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                ],
                [
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                ],
                1,
                null,
            ],
            'preserve keys on maxresult only' => [
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                ],
                [
                    'a' => 1,
                    'b' => 2,
                ],
                null,
                2,
            ],
        ];
    }

    public function testMultiColumnSortAppliesAllSorts(): void
    {
        $collection = $this->buildCollection([
            ['foo' => 1, 'bar' => 2],
            ['foo' => 2, 'bar' => 4],
            ['foo' => 2, 'bar' => 3],
        ]);

        $expected = [
            1 => ['foo' => 2, 'bar' => 4],
            2 => ['foo' => 2, 'bar' => 3],
            0 => ['foo' => 1, 'bar' => 2],
        ];

        if (! $this->isSelectable($collection)) {
            $this->markTestSkipped('Collection does not support Selectable interface');
        }

        self::assertSame(
            $expected,
            $collection
                ->matching(new Criteria(null, ['foo' => Order::Descending, 'bar' => Order::Descending]))
                ->toArray(),
        );
    }
}
