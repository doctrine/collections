<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use function count;
use function is_string;

class CollectionTest extends BaseCollectionTest
{
    protected function setUp() : void
    {
        $this->collection = new ArrayCollection();
    }

    public function testToString() : void
    {
        $this->collection->add('testing');
        self::assertTrue(is_string((string) $this->collection));
    }

    /**
     * @group DDC-1637
     */
    public function testMatching() : void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq('foo', 'bar')));
        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
    }

    /**
     * @group DDC-1637
     */
    public function testMatchingOrdering() : void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, ['foo' => 'DESC']));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(2, count($col));
        self::assertEquals('baz', $col->first()->foo);
        self::assertEquals('bar', $col->last()->foo);
    }

    /**
     * @group DDC-1637
     */
    public function testMatchingSlice() : void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, null, 1, 1));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
        self::assertEquals('baz', $col[0]->foo);
    }
}
