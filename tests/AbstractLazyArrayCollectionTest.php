<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

use function assert;

/**
 * Tests for {@see AbstractLazyCollection}.
 */
#[CoversClass(AbstractLazyCollection::class)]
class AbstractLazyArrayCollectionTest extends ArrayCollectionTestCase
{
    #[Override]
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
