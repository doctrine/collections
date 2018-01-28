<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Tests\LazyArrayCollection;

/**
 * Tests for {@see \Doctrine\Common\Collections\AbstractLazyCollection}.
 *
 * @covers \Doctrine\Common\Collections\AbstractLazyCollection
 */
class AbstractLazyArrayCollectionTest extends BaseArrayCollectionTest
{
    protected function buildCollection(array $elements = []) : Collection
    {
        return new LazyArrayCollection(new ArrayCollection($elements));
    }

    public function testLazyCollection() : void
    {
        /** @var LazyArrayCollection $collection */
        $collection = $this->buildCollection(['a', 'b', 'c']);

        self::assertFalse($collection->isInitialized());
        self::assertCount(3, $collection);
    }
}
