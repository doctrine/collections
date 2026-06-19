<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\Common\Collections\Order;
use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use PHPUnit\Framework\Attributes\Group;
use SortDirection;

use function count;
use function is_string;

class CollectionTest extends CollectionTestCase
{
    use VerifyDeprecations;

    protected function setUp(): void
    {
        $this->collection = new ArrayCollection::<int|string, int|float|string|bool|array|object|null>();
    }

    public function testToString(): void
    {
        $this->collection->add('testing');
        self::assertTrue(is_string((string) $this->collection));
    }

    public function testMatching(): void
    {
        $this->collection[] = new TestObjectPrivatePropertyOnly(42);
        $this->collection[] = new TestObjectPrivatePropertyOnly(84);

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq('fooBar', 42)));
        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
    }

    public function testMatchingCallable(): void
    {
        $this->fillMatchingFixture();
        $this->collection[0]->foo = 1;

        $col = $this->collection->matching(
            new Criteria(new Value(static fn (TestObject $test): bool => $test->foo === 1)),
        );

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
    }

    #[Group('DDC-1637')]
    public function testMatchingOrdering(): void
    {
        $this->collection['one']   = $obj1 = new TestObjectPrivatePropertyOnly(18);
        $this->collection['two']   = $obj2 = new TestObjectPrivatePropertyOnly(10);
        $this->collection['three'] = $obj3 = new TestObjectPrivatePropertyOnly(78);

        $criteria = Criteria::create()->orderBy(['fooBar' => SortDirection::Ascending]);

        $col = $this->collection->matching($criteria);

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(['two' => $obj2, 'one' => $obj1, 'three' => $obj3], $col->toArray());
    }

    #[Group('DDC-1637')]
    public function testMatchingOrderingLegacy(): void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, ['foo' => SortDirection::Descending]));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(2, count($col));
        self::assertEquals('baz', $col->first()->foo);
        self::assertEquals('bar', $col->last()->foo);
    }

    #[Group('DDC-1637')]
    public function testMatchingSlice(): void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, null, 1, 1));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
        self::assertEquals('baz', $col[1]->foo);
    }

    public function testMatchingOrderingMixedOrderTypes(): void
    {
        $this->collection[] = $obj1 = new TestObject();
        $obj1->foo = 'c';
        $obj1->bar = 1;

        $this->collection[] = $obj2 = new TestObject();
        $obj2->foo = 'a';
        $obj2->bar = 2;

        $this->collection[] = $obj3 = new TestObject();
        $obj3->foo = 'b';
        $obj3->bar = 3;

        $criteria = Criteria::create()->orderBy([
            'foo' => Order::Ascending,
            'bar' => SortDirection::Descending,
        ]);

        $col = $this->collection->matching($criteria);

        self::assertInstanceOf(Collection::class, $col);
        self::assertCount(3, $col);
        self::assertSame($obj2, $col->first());
        self::assertSame($obj1, $col->last());
    }
}
