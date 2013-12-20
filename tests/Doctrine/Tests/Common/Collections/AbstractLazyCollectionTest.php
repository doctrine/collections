<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Tests\LazyCollection;

class AbstractLazyCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLazyCollection()
    {
        $collection = new LazyCollection();

        $this->assertCount(3, $collection);

        $collection->add('bar');
        $this->assertCount(4, $collection);
    }
}
