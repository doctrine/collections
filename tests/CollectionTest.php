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
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use stdClass;

use function count;
use function is_string;

class CollectionTest extends CollectionTestCase
{
    use VerifyDeprecations;

    protected function setUp(): void
    {
        $this->collection = new ArrayCollection();
    }

    public function testToString(): void
    {
        $this->collection->add('testing');
        self::assertTrue(is_string((string) $this->collection));
    }

    #[Group('DDC-1637')]
    #[IgnoreDeprecations]
    public function testMatchingLegacy(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/472');

        $std1               = new stdClass();
        $std1->foo          = 'bar';
        $this->collection[] = $std1;

        $std2               = new stdClass();
        $std2->foo          = 'baz';
        $this->collection[] = $std2;

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq('foo', 'bar')));
        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
    }

    public function testMatching(): void
    {
        $this->collection[] = new TestObjectPrivatePropertyOnly(42);
        $this->collection[] = new TestObjectPrivatePropertyOnly(84);

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq('fooBar', 42), firstResult: 0, accessRawFieldValues: true));
        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
    }

    public function testMatchingCallable(): void
    {
        $this->fillMatchingFixture();
        $this->collection[0]->foo = 1;

        $col = $this->collection->matching(
            new Criteria(
                new Value(static fn (stdClass $test): bool => $test->foo === 1),
                firstResult: 0,
                accessRawFieldValues: true,
            ),
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

        $criteria = Criteria::create(true)->orderBy(['fooBar' => Order::Ascending]);

        $col = $this->collection->matching($criteria);

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(['two' => $obj2, 'one' => $obj1, 'three' => $obj3], $col->toArray());
    }

    #[Group('DDC-1637')]
    #[IgnoreDeprecations]
    public function testMatchingOrderingLegacy(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/472');

        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, ['foo' => Order::Descending]));

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

        $col = $this->collection->matching(new Criteria(null, null, 1, 1, accessRawFieldValues: true));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
        self::assertEquals('baz', $col[1]->foo);
    }
}
