<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\LazyArrayCollection;

use function assert;
use function is_array;
use function is_numeric;
use function is_string;

/**
 * Tests for {@see \Doctrine\Common\Collections\AbstractLazyCollection}.
 *
 * @covers \Doctrine\Common\Collections\AbstractLazyCollection
 */
class AbstractLazyCollectionTest extends BaseCollectionTest
{
    protected function setUp(): void
    {
        $this->collection = new LazyArrayCollection(new ArrayCollection());
    }

    /** @psalm-param mixed[] $elements */
    private function buildCollection(array $elements): LazyArrayCollection
    {
        return new LazyArrayCollection(new ArrayCollection($elements));
    }

    public function testClearInitializes(): void
    {
        $collection = $this->buildCollection(['a', 'b', 'c']);
        assert($collection instanceof LazyArrayCollection);

        $collection->clear();

        self::assertTrue($collection->isInitialized());
        self::assertCount(0, $collection);
    }

    public function testFilterInitializes(): void
    {
        $collection = $this->buildCollection([1, 'foo', 3]);
        assert($collection instanceof LazyArrayCollection);

        $res = $collection->filter(static fn ($value) => is_numeric($value));

        self::assertEquals([0 => 1, 2 => 3], $res->toArray());
    }

    public function testForAllInitializes(): void
    {
        $collection = $this->buildCollection(['foo', 'bar']);

        self::assertEquals($collection->forAll(static fn ($k, $e) => is_string($e)), true);

        self::assertEquals($collection->forAll(static fn ($k, $e) => is_array($e)), false);
    }

    public function testMapInitializes(): void
    {
        $collection = $this->buildCollection([1, 2]);

        $res = $collection->map(static fn ($e) => $e * 2);
        self::assertEquals([2, 4], $res->toArray());
    }

    public function testPartitionInitializes(): void
    {
        $collection = $this->buildCollection([true, false]);
        $partition  = $collection->partition(static fn ($k, $e) => $e === true);
        self::assertEquals($partition[0][0], true);
        self::assertEquals($partition[1][0], false);
    }

    public function testSliceInitializes(): void
    {
        $collection = $this->buildCollection(['one', 'two', 'three']);

        $slice = $collection->slice(0, 1);
        self::assertIsArray($slice);
        self::assertEquals(['one'], $slice);

        $slice = $collection->slice(1);
        self::assertEquals([1 => 'two', 2 => 'three'], $slice);

        $slice = $collection->slice(1, 1);
        self::assertEquals([1 => 'two'], $slice);
    }

    public function testGetInitializes(): void
    {
        $value      = 'foo';
        $collection = $this->buildCollection([$value]);
        $this->assertSame($value, $collection[0]);
    }

    public function testUnsetInitializes(): void
    {
        $collection = $this->buildCollection(['foo', 'bar']);

        $collection->offsetUnset(0);
        self::assertCount(1, $collection);
        self::assertFalse(isset($collection[0]));
    }
}
