<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;

use function is_array;
use function is_numeric;
use function is_string;

/**
 * Tests for {@see AbstractLazyCollection}.
 */
#[CoversClass(AbstractLazyCollection::class)]
class AbstractLazyCollectionTest extends CollectionTestCase
{
    use VerifyDeprecations;

    protected function setUp(): void
    {
        $this->collection = new LazyArrayCollection(new ArrayCollection());
    }

    /** @phpstan-param mixed[] $elements */
    private function buildCollection(array $elements): LazyArrayCollection
    {
        return new LazyArrayCollection(new ArrayCollection($elements));
    }

    public function testClearInitializes(): void
    {
        $collection = $this->buildCollection(['a', 'b', 'c']);

        $collection->clear();

        self::assertTrue($collection->isInitialized());
        self::assertCount(0, $collection);
    }

    public function testFilterInitializes(): void
    {
        $collection = $this->buildCollection([1, 'foo', 3]);

        $res = $collection->filter(static fn ($value) => is_numeric($value));

        self::assertEquals([0 => 1, 2 => 3], $res->toArray());
    }

    public function testForAllInitializes(): void
    {
        $collection = $this->buildCollection(['foo', 'bar']);

        self::assertEquals($collection->forAll(static fn ($k, $e) => is_string($e)), true);

        self::assertEquals($collection->forAll(static fn ($k, $e) => is_array($e)), false);
    }

    public function testMapInitializes(): void
    {
        $collection = $this->buildCollection([1, 2]);

        $res = $collection->map(static fn ($e) => $e * 2);
        self::assertEquals([2, 4], $res->toArray());
    }

    public function testPartitionInitializes(): void
    {
        $collection = $this->buildCollection([true, false]);
        $partition  = $collection->partition(static fn ($k, $e) => $e === true);
        self::assertEquals($partition[0][0], true);
        self::assertEquals($partition[1][0], false);
    }

    public function testSliceInitializes(): void
    {
        $collection = $this->buildCollection(['one', 'two', 'three']);

        $slice = $collection->slice(0, 1);
        self::assertIsArray($slice);
        self::assertEquals(['one'], $slice);

        $slice = $collection->slice(1);
        self::assertEquals([1 => 'two', 2 => 'three'], $slice);

        $slice = $collection->slice(1, 1);
        self::assertEquals([1 => 'two'], $slice);
    }

    public function testGetInitializes(): void
    {
        $value      = 'foo';
        $collection = $this->buildCollection([$value]);
        $this->assertSame($value, $collection[0]);
    }

    public function testUnsetInitializes(): void
    {
        $collection = $this->buildCollection(['foo', 'bar']);

        $collection->offsetUnset(0);
        self::assertCount(1, $collection);
        self::assertFalse(isset($collection[0]));
    }

    public function testMatchingInitializes(): void
    {
        $collection = $this->buildCollection(['foo', 'bar', 'baz']);

        self::assertFalse($collection->isInitialized());

        $result = $collection->matching(Criteria::create(true));

        self::assertTrue($collection->isInitialized());
        self::assertCount(3, $result);
    }

    public function testMatchingWithCriteria(): void
    {
        $obj1 = new TestObjectPrivatePropertyOnly('bar');
        $obj2 = new TestObjectPrivatePropertyOnly('baz');
        $obj3 = new TestObjectPrivatePropertyOnly('bar');

        $collection = $this->buildCollection([$obj1, $obj2, $obj3]);

        $criteria = Criteria::create(true)
            ->where(Criteria::expr()->eq('fooBar', 'bar'));

        $result = $collection->matching($criteria);

        self::assertCount(2, $result);
        self::assertSame($obj1, $result->first());
    }

    #[IgnoreDeprecations]
    public function testMatchingThrowsExceptionWhenBackedCollectionNotSelectable(): void
    {
        $lazyCollection = new LazyArrayCollection($this->createStub(Collection::class));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The backed collection must implement Selectable to use matching().');

        $lazyCollection->matching(Criteria::create(true));
    }

    #[IgnoreDeprecations]
    public function testMatchingTriggersDeprecationWhenBackedCollectionNotSelectable(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/518');

        $lazyCollection = new LazyArrayCollection($this->createStub(Collection::class));

        // Trigger initialization with any method - deprecation happens during initialize()
        $lazyCollection->isEmpty();
    }
}
