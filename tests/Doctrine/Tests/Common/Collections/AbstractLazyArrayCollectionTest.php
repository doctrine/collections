<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Tests\LazyArrayCollection;

use function assert;

/**
 * Tests for {@see \Doctrine\Common\Collections\AbstractLazyCollection}.
 *
 * @covers \Doctrine\Common\Collections\AbstractLazyCollection
 */
class AbstractLazyArrayCollectionTest extends BaseArrayCollectionTest
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<mixed>
     */
    protected function buildCollection(array $elements = []): Collection
    {
        return new LazyArrayCollection(new ArrayCollection($elements));
    }

    public function testLazyCollection(): void
    {
        $collection = $this->buildCollection(['a', 'b', 'c']);
        assert($collection instanceof LazyArrayCollection);

        self::assertFalse($collection->isInitialized());
        self::assertCount(3, $collection);
    }
}
