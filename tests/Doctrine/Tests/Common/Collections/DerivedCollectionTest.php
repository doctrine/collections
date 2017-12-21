<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Tests\DerivedArrayCollection;

/**
 * @author Alexander Golovnya <snsanich@gmail.com>
 */
class DerivedCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that methods that create a new instance can be called in a derived
     * class that implements different constructor semantics.
     */
    public function testDerivedClassCreation() : void
    {
        $collection = new DerivedArrayCollection(new \stdClass());
        $closure    = function () {
            return $allMatches = false;
        };

        self::assertInstanceOf(DerivedArrayCollection::class, $collection->map($closure));
        self::assertInstanceOf(DerivedArrayCollection::class, $collection->filter($closure));
        self::assertContainsOnlyInstancesOf(DerivedArrayCollection::class, $collection->partition($closure));
        self::assertInstanceOf(DerivedArrayCollection::class, $collection->matching(new Criteria()));
    }
}
