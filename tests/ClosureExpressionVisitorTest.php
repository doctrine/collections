<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use ArrayIterator;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

use function usort;

#[Group('DDC-1637')]
class ClosureExpressionVisitorTest extends TestCase
{
    use VerifyDeprecations;

    private ClosureExpressionVisitor $visitor;

    private ExpressionBuilder $builder;

    protected function setUp(): void
    {
        $this->visitor = new ClosureExpressionVisitor(true);
        $this->builder = new ExpressionBuilder();
    }

    public function testEmbeddedObjectComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->eq('foo.foo', 1));
        $this->assertTrue($closure(new TestObject(new TestObject(1))));
        $this->assertFalse($closure(new TestObject(new TestObject(2))));
    }

    #[IgnoreDeprecations]
    public function testGetObjectFieldValueLegacy(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/486');

        $object = new TestObject();

        $this->visitor->getObjectFieldValue($object, 'foo', true);
    }

    public function testGetEmbeddedObjectFieldValueAccessingRawValue(): void
    {
        $object = new TestObject(new TestObjectPrivatePropertyOnly(42));

        self::assertSame(42, $this->visitor->getObjectFieldValue($object, 'foo.fooBar'));
    }

    public function testGetObjectFieldValueAccessingRawValueBypassingPropertyHook(): void
    {
        $object         = new TestObjectPropertyHook();
        $object->fooBar = 42;

        self::assertSame(42, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueAccessingRawValue(): void
    {
        $object = new TestObjectPrivatePropertyOnly(42);

        self::assertSame(42, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueFindingParentClassAccessingRawValue(): void
    {
        $object = new TestObjectWithPrivatePropertyInParentClass(42);

        self::assertSame(42, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueNonexistentFieldAccessingRawValue(): void
    {
        $object = new stdClass();

        $this->expectException(RuntimeException::class);

        $this->visitor->getObjectFieldValue($object, 'fooBar');
    }

    public function testWalkEqualsComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->eq('foo', 1));

        self::assertTrue($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(2)));
    }

    public function testWalkNotEqualsComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->neq('foo', 1));

        self::assertFalse($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(2)));
    }

    public function testWalkLessThanComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->lt('foo', 1));

        self::assertFalse($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(0)));
    }

    public function testWalkLessThanEqualsComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->lte('foo', 1));

        self::assertFalse($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanEqualsComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->gte('foo', 1));

        self::assertTrue($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->gt('foo', 1));

        self::assertTrue($closure(new TestObject(2)));
        self::assertFalse($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(0)));
    }

    public function testWalkInComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->in('foo', [1, 2, 3, '04']));

        self::assertTrue($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(0)));
        self::assertFalse($closure(new TestObject(4)));
        self::assertTrue($closure(new TestObject('04')));
    }

    public function testWalkInComparisonObjects(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->in('foo', [new TestObject(1), new TestObject(2), new TestObject(4)]));

        self::assertTrue($closure(new TestObject(new TestObject(2))));
        self::assertTrue($closure(new TestObject(new TestObject(1))));
        self::assertFalse($closure(new TestObject(new TestObject(0))));
        self::assertTrue($closure(new TestObject(new TestObject(4))));
        self::assertFalse($closure(new TestObject(new TestObject('baz'))));
    }

    public function testWalkNotInComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->notIn('foo', [1, 2, 3, '04']));

        self::assertFalse($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(0)));
        self::assertTrue($closure(new TestObject(4)));
        self::assertFalse($closure(new TestObject('04')));
    }

    public function testWalkNotInComparisonObjects(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->notIn('foo', [new TestObject(1), new TestObject(2), new TestObject(4)]));

        self::assertFalse($closure(new TestObject(new TestObject(1))));
        self::assertFalse($closure(new TestObject(new TestObject(2))));
        self::assertTrue($closure(new TestObject(new TestObject(0))));
        self::assertFalse($closure(new TestObject(new TestObject(4))));
        self::assertTrue($closure(new TestObject(new TestObject('baz'))));
    }

    public function testWalkContainsComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->contains('foo', 'hello'));

        self::assertTrue($closure(new TestObject('hello world')));
        self::assertFalse($closure(new TestObject('world')));
    }

    public function testWalkMemberOfComparisonWithObject(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->memberof('foo', 2));

        self::assertTrue($closure(new TestObject([1, 2, 3])));
        self::assertTrue($closure(new TestObject([2])));
        self::assertTrue($closure(new TestObject(new ArrayIterator([2]))));
        self::assertFalse($closure(new TestObject([1, 3, 5])));
        self::assertFalse($closure(new TestObject([1, '02'])));
        self::assertFalse($closure(new TestObject(new ArrayIterator([4]))));
    }

    public function testWalkStartsWithComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->startsWith('foo', 'hello'));

        self::assertTrue($closure(new TestObject('hello world')));
        self::assertFalse($closure(new TestObject('world')));
    }

    public function testWalkEndsWithComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->endsWith('foo', 'world'));

        self::assertTrue($closure(new TestObject('hello world')));
        self::assertFalse($closure(new TestObject('hello')));
    }

    public function testWalkUnknownOperatorComparisonThrowException(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unknown comparison operator: unknown');

        $closure = $this->visitor->walkComparison(new Comparison('foo', 'unknown', 2));

        $closure(new TestObject(2));
    }

    public function testWalkAndCompositeExpression(): void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->andX(
                $this->builder->eq('foo', 1),
                $this->builder->eq('bar', 1),
            ),
        );

        self::assertTrue($closure(new TestObject(1, 1)));
        self::assertFalse($closure(new TestObject(1, 0)));
        self::assertFalse($closure(new TestObject(0, 1)));
        self::assertFalse($closure(new TestObject(0, 0)));
    }

    public function testWalkOrCompositeExpression(): void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->orX(
                $this->builder->eq('foo', 1),
                $this->builder->eq('bar', 1),
            ),
        );

        self::assertTrue($closure(new TestObject(1, 1)));
        self::assertTrue($closure(new TestObject(1, 0)));
        self::assertTrue($closure(new TestObject(0, 1)));
        self::assertFalse($closure(new TestObject(0, 0)));
    }

    public function testWalkOrAndCompositeExpression(): void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->orX(
                $this->builder->andX(
                    $this->builder->eq('foo', 1),
                    $this->builder->eq('bar', 1),
                ),
                $this->builder->andX(
                    $this->builder->eq('foo', 2),
                    $this->builder->eq('bar', 2),
                ),
            ),
        );

        self::assertTrue($closure(new TestObject(1, 1)));
        self::assertTrue($closure(new TestObject(2, 2)));
        self::assertFalse($closure(new TestObject(1, 2)));
        self::assertFalse($closure(new TestObject(2, 1)));
        self::assertFalse($closure(new TestObject(0, 0)));
    }

    public function testWalkAndOrCompositeExpression(): void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->andX(
                $this->builder->orX(
                    $this->builder->eq('foo', 1),
                    $this->builder->eq('foo', 2),
                ),
                $this->builder->orX(
                    $this->builder->eq('bar', 3),
                    $this->builder->eq('bar', 4),
                ),
            ),
        );

        self::assertTrue($closure(new TestObject(1, 3)));
        self::assertTrue($closure(new TestObject(1, 4)));
        self::assertTrue($closure(new TestObject(2, 3)));
        self::assertTrue($closure(new TestObject(2, 4)));
        self::assertFalse($closure(new TestObject(1, 0)));
        self::assertFalse($closure(new TestObject(2, 0)));
        self::assertFalse($closure(new TestObject(0, 3)));
        self::assertFalse($closure(new TestObject(0, 4)));
    }

    public function testWalkNotCompositeExpression(): void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->not(
                $this->builder->eq('foo', 1),
            ),
        );

        self::assertFalse($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(0)));
    }

    public function testWalkUnknownCompositeExpressionThrowException(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unknown composite Unknown');

        $closure = $this->visitor->walkCompositeExpression(
            new CompositeExpression('Unknown', []),
        );

        $closure(new TestObject());
    }

    #[IgnoreDeprecations]
    public function testSortByFieldLegacy(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/486');

        $objects = [new TestObject('b'), new TestObject('a')];
        $sort    = ClosureExpressionVisitor::sortByField('foo', 1, null, true);

        usort($objects, $sort);
    }

    public function testSortByFieldAscending(): void
    {
        $objects = [new TestObject('b'), new TestObject('a'), new TestObject('c')];
        $sort    = ClosureExpressionVisitor::sortByField('foo', 1);

        usort($objects, $sort);

        self::assertEquals('a', $objects[0]->foo);
        self::assertEquals('b', $objects[1]->foo);
        self::assertEquals('c', $objects[2]->foo);
    }

    public function testSortByFieldDescending(): void
    {
        $objects = [new TestObject('b'), new TestObject('a'), new TestObject('c')];
        $sort    = ClosureExpressionVisitor::sortByField('foo', -1);

        usort($objects, $sort);

        self::assertEquals('c', $objects[0]->foo);
        self::assertEquals('b', $objects[1]->foo);
        self::assertEquals('a', $objects[2]->foo);
    }

    public function testSortByFieldKeepOrderWhenSameValue(): void
    {
        $firstElement  = new TestObject('a');
        $secondElement = new TestObject('a');

        $objects = [$firstElement, $secondElement];
        $sort    = ClosureExpressionVisitor::sortByField('foo', 0);

        usort($objects, $sort);

        self::assertSame([$firstElement, $secondElement], $objects);
    }

    public function testSortDelegate(): void
    {
        $objects = [new TestObject('a', 'c'), new TestObject('a', 'b'), new TestObject('a', 'a')];
        $sort    = ClosureExpressionVisitor::sortByField('bar', 1);
        $sort    = ClosureExpressionVisitor::sortByField('foo', 1, $sort);

        usort($objects, $sort);

        self::assertEquals('a', $objects[0]->bar);
        self::assertEquals('b', $objects[1]->bar);
        self::assertEquals('c', $objects[2]->bar);
    }

    public function testArrayComparison(): void
    {
        $closure = $this->visitor->walkComparison($this->builder->eq('foo', 42));

        self::assertTrue($closure(['foo' => 42]));
    }
}

class TestObjectWithPrivatePropertyInParentClass extends TestObjectPrivatePropertyOnly
{
}
