<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\Value;
use RuntimeException;
use stdClass;

use function count;
use function is_string;

class CollectionTest extends BaseCollectionTest
{
    protected function setUp(): void
    {
        $this->collection = new ArrayCollection();
    }

    public function testToString(): void
    {
        $this->collection->add('testing');
        self::assertTrue(is_string((string) $this->collection));
    }

    /** @group DDC-1637 */
    public function testMatching(): void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq('foo', 'bar')));
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
                new Value(static fn (stdClass $test): bool => $test->foo === 1)
            ),
        );

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
    }

    public function testMatchingUnknownThrowException(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unknown Expression GenericExpression');

        $genericExpression = $this->getMockBuilder(Expression::class)
            ->setMockClassName('GenericExpression')
            ->getMock();

        $this->collection->matching(new Criteria($genericExpression));
    }

    /** @group DDC-1637 */
    public function testMatchingOrdering(): void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, ['foo' => 'DESC']));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(2, count($col));
        self::assertEquals('baz', $col->first()->foo);
        self::assertEquals('bar', $col->last()->foo);
    }

    /** @group DDC-1637 */
    public function testMatchingSlice(): void
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, null, 1, 1));

        self::assertInstanceOf(Collection::class, $col);
        self::assertNotSame($col, $this->collection);
        self::assertEquals(1, count($col));
        self::assertEquals('baz', $col[1]->foo);
    }
}
