<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Collection;

abstract class BaseCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    protected $collection;

    public function testIssetAndUnset() : void
    {
        $this->assertFalse(isset($this->collection[0]));
        $this->collection->add('testing');
        $this->assertTrue(isset($this->collection[0]));
        unset($this->collection[0]);
        $this->assertFalse(isset($this->collection[0]));
    }

    public function testRemovingNonExistentEntryReturnsNull() : void
    {
        $this->assertEquals(null, $this->collection->remove('testing_does_not_exist'));
    }

    public function testExists() : void
    {
        $this->collection->add('one');
        $this->collection->add('two');
        $exists = $this->collection->exists(function ($k, $e) {
            return $e == 'one';
        });
        $this->assertTrue($exists);
        $exists = $this->collection->exists(function ($k, $e) {
            return $e == 'other';
        });
        $this->assertFalse($exists);
    }

    public function testMap() : void
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $res = $this->collection->map(function ($e) {
            return $e * 2;
        });
        $this->assertEquals([2, 4], $res->toArray());
    }

    public function testReduce() : void
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $res = $this->collection->reduce(function($carry, $e) {
            $carry += $e;
            return $carry;
        }, 1);
        $this->assertSame(4, $res);
    }

    public function testFilter() : void
    {
        $this->collection->add(1);
        $this->collection->add('foo');
        $this->collection->add(3);
        $res = $this->collection->filter(function ($e) {
            return is_numeric($e);
        });
        $this->assertEquals([0 => 1, 2 => 3], $res->toArray());
    }

    public function testFirstAndLast() : void
    {
        $this->collection->add('one');
        $this->collection->add('two');

        $this->assertEquals($this->collection->first(), 'one');
        $this->assertEquals($this->collection->last(), 'two');
    }

    public function testArrayAccess() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertEquals($this->collection[0], 'one');
        $this->assertEquals($this->collection[1], 'two');

        unset($this->collection[0]);
        $this->assertEquals($this->collection->count(), 1);
    }

    public function testContainsKey() : void
    {
        $this->collection[5] = 'five';
        $this->assertTrue($this->collection->containsKey(5));
    }

    public function testContains() : void
    {
        $this->collection[0] = 'test';
        $this->assertTrue($this->collection->contains('test'));
    }

    public function testSearch() : void
    {
        $this->collection[0] = 'test';
        $this->assertEquals(0, $this->collection->indexOf('test'));
    }

    public function testGet() : void
    {
        $this->collection[0] = 'test';
        $this->assertEquals('test', $this->collection->get(0));
    }

    public function testGetKeys() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals([0, 1], $this->collection->getKeys());
    }

    public function testGetValues() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals(['one', 'two'], $this->collection->getValues());
    }

    public function testCount() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->count(), 2);
        $this->assertEquals(count($this->collection), 2);
    }

    public function testForAll() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->forAll(function ($k, $e) {
            return is_string($e);
        }), true);
        $this->assertEquals($this->collection->forAll(function ($k, $e) {
            return is_array($e);
        }), false);
    }

    public function testPartition() : void
    {
        $this->collection[] = true;
        $this->collection[] = false;
        $partition = $this->collection->partition(function ($k, $e) {
            return $e == true;
        });
        $this->assertEquals($partition[0][0], true);
        $this->assertEquals($partition[1][0], false);
    }

    public function testClear() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection->clear();
        $this->assertEquals($this->collection->isEmpty(), true);
    }

    public function testRemove() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $el = $this->collection->remove(0);

        $this->assertEquals('one', $el);
        $this->assertEquals($this->collection->contains('one'), false);
        $this->assertNull($this->collection->remove(0));
    }

    public function testRemoveElement() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertTrue($this->collection->removeElement('two'));
        $this->assertFalse($this->collection->contains('two'));
        $this->assertFalse($this->collection->removeElement('two'));
    }

    public function testSlice() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection[] = 'three';

        $slice = $this->collection->slice(0, 1);
        $this->assertInternalType('array', $slice);
        $this->assertEquals(['one'], $slice);

        $slice = $this->collection->slice(1);
        $this->assertEquals([1 => 'two', 2 => 'three'], $slice);

        $slice = $this->collection->slice(1, 1);
        $this->assertEquals([1 => 'two'], $slice);
    }

    protected function fillMatchingFixture() : void
    {
        $std1 = new \stdClass();
        $std1->foo = 'bar';
        $this->collection[] = $std1;

        $std2 = new \stdClass();
        $std2->foo = 'baz';
        $this->collection[] = $std2;
    }

    public function testCanRemoveNullValuesByKey() : void
    {
        $this->collection->add(null);
        $this->collection->remove(0);
        $this->assertTrue($this->collection->isEmpty());
    }

    public function testCanVerifyExistingKeysWithNullValues() : void
    {
        $this->collection->set('key', null);
        $this->assertTrue($this->collection->containsKey('key'));
    }
}
