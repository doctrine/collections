<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Tests\LazyArrayCollection;

class AbstractLazyCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLazyCollection()
    {
        $collection = new LazyArrayCollection();

        $this->assertCount(3, $collection);

        $collection->add('bar');
        $this->assertCount(4, $collection);
    }
}
