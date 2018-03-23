<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\LazyArrayCollection;

/**
 * Tests for {@see \Doctrine\Common\Collections\AbstractLazyCollection}.
 *
 * @covers \Doctrine\Common\Collections\AbstractLazyCollection
 */
class AbstractLazyCollectionTest extends BaseCollectionTest
{
    protected function setUp() : void
    {
        $this->collection = new LazyArrayCollection(new ArrayCollection());
    }
}
