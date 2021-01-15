<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Tests for {@see \Doctrine\Common\Collections\ArrayCollection}.
 *
 * @covers \Doctrine\Common\Collections\ArrayCollection
 */
class ArrayCollectionTest extends BaseArrayCollectionTest
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<mixed>
     */
    protected function buildCollection(array $elements = []): Collection
    {
        return new ArrayCollection($elements);
    }
}
