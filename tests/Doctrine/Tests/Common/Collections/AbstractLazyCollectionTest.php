<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Tests\DoctrineTestCase;
use Doctrine\Tests\LazyArrayCollection;

class AbstractLazyCollectionTest extends DoctrineTestCase
{
    public function testLazyCollection()
    {
        $collection = new LazyArrayCollection();

        $this->assertFalse($collection->isInitialized());
        $this->assertCount(3, $collection);

        $collection->add('bar');
        $this->assertTrue($collection->isInitialized());
        $this->assertCount(4, $collection);
    }
}
