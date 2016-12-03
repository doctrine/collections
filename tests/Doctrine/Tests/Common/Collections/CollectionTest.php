<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class CollectionTest extends BaseCollectionTest
{
    protected function setUp()
    {
        $this->collection = new ArrayCollection();
    }

    public function testToString()
    {
        $this->collection->add('testing');
        $this->assertTrue(is_string((string) $this->collection));
    }

    /**
     * @group DDC-1637
     */
    public function testMatching()
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq("foo", "bar")));
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $col);
        $this->assertNotSame($col, $this->collection);
        $this->assertEquals(1, count($col));
    }

    /**
     * @group DDC-1637
     */
    public function testMatchingOrdering()
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, array('foo' => 'DESC')));

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $col);
        $this->assertNotSame($col, $this->collection);
        $this->assertEquals(2, count($col));
        $this->assertEquals('baz', $col->first()->foo);
        $this->assertEquals('bar', $col->last()->foo);
    }

    /**
     * @group DDC-1637
     */
    public function testMatchingSlice()
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, null, 1, 1));

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $col);
        $this->assertNotSame($col, $this->collection);
        $this->assertEquals(1, count($col));
        $this->assertEquals('baz', $col[0]->foo);
    }
}
