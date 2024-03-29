<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;
use stdClass;

class DerivedCollectionTest extends TestCase
{
    /**
     * Tests that methods that create a new instance can be called in a derived
     * class that implements different constructor semantics.
     */
    public function testDerivedClassCreation(): void
    {
        $collection = new DerivedArrayCollection(new stdClass());
        $closure    = static fn () => $allMatches = false;

        self::assertInstanceOf(DerivedArrayCollection::class, $collection->map($closure));
        self::assertInstanceOf(DerivedArrayCollection::class, $collection->filter($closure));
        self::assertContainsOnlyInstancesOf(DerivedArrayCollection::class, $collection->partition($closure));
        self::assertInstanceOf(DerivedArrayCollection::class, $collection->matching(new Criteria()));
    }
}
