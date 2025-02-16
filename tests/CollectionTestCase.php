<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use PHPUnit\Framework\TestCase;
use stdClass;

use function count;
use function is_array;
use function is_numeric;
use function is_string;
use function sprintf;

abstract class CollectionTestCase extends TestCase
{
    /** @var Collection<mixed> */
    protected Collection $collection;

    public function testIssetAndUnset(): void
    {
        self::assertFalse(isset($this->collection[0]));
        $this->collection->add('testing');
        self::assertTrue(isset($this->collection[0]));
        unset($this->collection[0]);
        self::assertFalse(isset($this->collection[0]));
    }

    public function testRemovingNonExistentEntryReturnsNull(): void
    {
        self::assertEquals(null, $this->collection->remove('testing_does_not_exist'));
    }

    public function testExists(): void
    {
        $this->collection->add('one');
        $this->collection->add('two');
        $exists = $this->collection->exists(static fn ($k, $e) => $e === 'one');
        self::assertTrue($exists);
        $exists = $this->collection->exists(static fn ($k, $e) => $e === 'other');
        self::assertFalse($exists);
    }

    public function testFindFirst(): void
    {
        $this->collection->add('one');
        $this->collection->add('two');
        $one = $this->collection->findFirst(static fn ($k, $e) => $e === 'one');
        self::assertSame('one', $one);
    }

    public function testFindFirstNotFound(): void
    {
        $this->collection->add('one');
        $this->collection->add('two');
        $other = $this->collection->findFirst(static fn ($k, $e) => $e === 'other');
        self::assertNull($other);
    }

    public function testMap(): void
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $res = $this->collection->map(static fn ($e) => $e * 2);
        self::assertEquals([2, 4], $res->toArray());
    }

    public function testReduce(): void
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);
        $this->collection->add(4);

        $res = $this->collection->reduce(static fn ($sum, $e) => $sum + $e);
        self::assertSame(10, $res);
    }

    public function testFilter(): void
    {
        $this->collection->add(1);
        $this->collection->add('foo');
        $this->collection->add(3);
        $res = $this->collection->filter(static fn ($e) => is_numeric($e));
        self::assertEquals([0 => 1, 2 => 3], $res->toArray());
    }

    public function testFilterByValueAndKey(): void
    {
        $this->collection->add(1);
        $this->collection->add('foo');
        $this->collection->add(3);
        $this->collection->add(4);
        $this->collection->add(5);
        $res = $this->collection->filter(static fn ($v, $k) => is_numeric($v) && $k % 2 === 0);
        self::assertSame([0 => 1, 2 => 3, 4 => 5], $res->toArray());
    }

    public function testFirstAndLast(): void
    {
        $this->collection->add('one');
        $this->collection->add('two');

        self::assertEquals($this->collection->first(), 'one');
        self::assertEquals($this->collection->last(), 'two');
    }

    public function testArrayAccess(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        self::assertEquals($this->collection[0], 'one');
        self::assertEquals($this->collection[1], 'two');

        unset($this->collection[0]);
        self::assertEquals($this->collection->count(), 1);
    }

    public function testContainsKey(): void
    {
        $this->collection[5] = 'five';
        self::assertTrue($this->collection->containsKey(5));
    }

    public function testContains(): void
    {
        $this->collection[0] = 'test';
        self::assertTrue($this->collection->contains('test'));
    }

    public function testSearch(): void
    {
        $this->collection[0] = 'test';
        self::assertEquals(0, $this->collection->indexOf('test'));
    }

    public function testGet(): void
    {
        $this->collection[0] = 'test';
        self::assertEquals('test', $this->collection->get(0));
    }

    public function testGetKeys(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals([0, 1], $this->collection->getKeys());
    }

    public function testGetValues(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals(['one', 'two'], $this->collection->getValues());
    }

    public function testCount(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals($this->collection->count(), 2);
        self::assertEquals(count($this->collection), 2);
    }

    public function testForAll(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals($this->collection->forAll(static fn ($k, $e) => is_string($e)), true);
        self::assertEquals($this->collection->forAll(static fn ($k, $e) => is_array($e)), false);
    }

    public function testPartition(): void
    {
        $this->collection[] = true;
        $this->collection[] = false;
        $partition          = $this->collection->partition(static fn ($k, $e) => $e === true);
        self::assertEquals($partition[0][0], true);
        self::assertEquals($partition[1][0], false);
    }

    public function testClear(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection->clear();
        self::assertEquals($this->collection->isEmpty(), true);
    }

    public function testRemove(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $el                 = $this->collection->remove(0);

        self::assertEquals('one', $el);
        self::assertEquals($this->collection->contains('one'), false);
        self::assertNull($this->collection->remove(0));
    }

    public function testRemoveElement(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        self::assertTrue($this->collection->removeElement('two'));
        self::assertFalse($this->collection->contains('two'));
        self::assertFalse($this->collection->removeElement('two'));
    }

    public function testSlice(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection[] = 'three';

        $slice = $this->collection->slice(0, 1);
        self::assertIsArray($slice);
        self::assertEquals(['one'], $slice);

        $slice = $this->collection->slice(1);
        self::assertEquals([1 => 'two', 2 => 'three'], $slice);

        $slice = $this->collection->slice(1, 1);
        self::assertEquals([1 => 'two'], $slice);
    }

    protected function fillMatchingFixture(): void
    {
        $std1               = new stdClass();
        $std1->foo          = 'bar';
        $this->collection[] = $std1;

        $std2               = new stdClass();
        $std2->foo          = 'baz';
        $this->collection[] = $std2;
    }

    public function testCanRemoveNullValuesByKey(): void
    {
        $this->collection->add(null);
        $this->collection->remove(0);
        self::assertTrue($this->collection->isEmpty());
    }

    public function testCanVerifyExistingKeysWithNullValues(): void
    {
        $this->collection->set('key', null);
        self::assertTrue($this->collection->containsKey('key'));
    }

    public function testMatchingAlwaysReturnsCollection(): void
    {
        if (! $this->collection instanceof Selectable) {
            self::markTestSkipped(sprintf('Collection does not implement %s', Selectable::class));
        }

        $criteria = Criteria::create();

        self::assertInstanceOf(Collection::class, $this->collection->matching($criteria));
        self::assertInstanceOf(Selectable::class, $this->collection->matching($criteria));
    }
}
