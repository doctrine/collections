<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\ExpressionBuilder;
use PHPUnit\Framework\TestCase;
use function usort;

/**
 * @group DDC-1637
 */
class ClosureExpressionVisitorTest extends TestCase
{
    /** @var ClosureExpressionVisitor */
    private $visitor;

    /** @var ExpressionBuilder */
    private $builder;

    protected function setUp() : void
    {
        $this->visitor = new ClosureExpressionVisitor();
        $this->builder = new ExpressionBuilder();
    }

    public function testGetObjectFieldValueIsAccessor() : void
    {
        $object = new TestObject(1, 2, true);

        self::assertTrue($this->visitor->getObjectFieldValue($object, 'baz'));
    }

    public function testGetObjectFieldValueIsAccessorWithIsPrefix() : void
    {
        $object = new TestObject(1, 2, true);

        self::assertTrue($this->visitor->getObjectFieldValue($object, 'isBaz'));
    }

    public function testGetObjectFieldValueIsAccessorCamelCase() : void
    {
        $object = new TestObjectNotCamelCase(1);

        self::assertEquals(1, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        self::assertEquals(1, $this->visitor->getObjectFieldValue($object, 'foobar'));
        self::assertEquals(1, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorBoth() : void
    {
        $object = new TestObjectBothCamelCaseAndUnderscore(1, 2);

        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foobar'));
        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorOnePublic() : void
    {
        $object = new TestObjectPublicCamelCaseAndPrivateUnderscore(1, 2);

        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foobar'));
        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorBothPublic() : void
    {
        $object = new TestObjectPublicCamelCaseAndPrivateUnderscore(1, 2);

        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foobar'));
        self::assertEquals(2, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueMagicCallMethod() : void
    {
        $object = new TestObject(1, 2, true, 3);

        self::assertEquals(3, $this->visitor->getObjectFieldValue($object, 'qux'));
    }

    public function testWalkEqualsComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->eq('foo', 1));

        self::assertTrue($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(2)));
    }

    public function testWalkNotEqualsComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->neq('foo', 1));

        self::assertFalse($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(2)));
    }

    public function testWalkLessThanComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->lt('foo', 1));

        self::assertFalse($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(0)));
    }

    public function testWalkLessThanEqualsComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->lte('foo', 1));

        self::assertFalse($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(1)));
        self::assertTrue($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanEqualsComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->gte('foo', 1));

        self::assertTrue($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->gt('foo', 1));

        self::assertTrue($closure(new TestObject(2)));
        self::assertFalse($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(0)));
    }

    public function testWalkInComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->in('foo', [1, 2, 3, '04']));

        self::assertTrue($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(0)));
        self::assertFalse($closure(new TestObject(4)));
        self::assertTrue($closure(new TestObject('04')));
    }

    public function testWalkNotInComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->notIn('foo', [1, 2, 3, '04']));

        self::assertFalse($closure(new TestObject(1)));
        self::assertFalse($closure(new TestObject(2)));
        self::assertTrue($closure(new TestObject(0)));
        self::assertTrue($closure(new TestObject(4)));
        self::assertFalse($closure(new TestObject('04')));
    }

    public function testWalkContainsComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->contains('foo', 'hello'));

        self::assertTrue($closure(new TestObject('hello world')));
        self::assertFalse($closure(new TestObject('world')));
    }

    public function testWalkMemberOfComparisonWithObject() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->memberof('foo', 2));

        self::assertTrue($closure(new TestObject([1, 2, 3])));
        self::assertTrue($closure(new TestObject([2])));
        self::assertFalse($closure(new TestObject([1, 3, 5])));
        self::assertFalse($closure(new TestObject([1, '02'])));
    }

    public function testWalkStartsWithComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->startsWith('foo', 'hello'));

        self::assertTrue($closure(new TestObject('hello world')));
        self::assertFalse($closure(new TestObject('world')));
    }

    public function testWalkEndsWithComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->endsWith('foo', 'world'));

        self::assertTrue($closure(new TestObject('hello world')));
        self::assertFalse($closure(new TestObject('hello')));
    }

    public function testWalkAndCompositeExpression() : void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->andX(
                $this->builder->eq('foo', 1),
                $this->builder->eq('bar', 1)
            )
        );

        self::assertTrue($closure(new TestObject(1, 1)));
        self::assertFalse($closure(new TestObject(1, 0)));
        self::assertFalse($closure(new TestObject(0, 1)));
        self::assertFalse($closure(new TestObject(0, 0)));
    }

    public function testWalkOrCompositeExpression() : void
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->orX(
                $this->builder->eq('foo', 1),
                $this->builder->eq('bar', 1)
            )
        );

        self::assertTrue($closure(new TestObject(1, 1)));
        self::assertTrue($closure(new TestObject(1, 0)));
        self::assertTrue($closure(new TestObject(0, 1)));
        self::assertFalse($closure(new TestObject(0, 0)));
    }

    public function testSortByFieldAscending() : void
    {
        $objects = [new TestObject('b'), new TestObject('a'), new TestObject('c')];
        $sort    = ClosureExpressionVisitor::sortByField('foo');

        usort($objects, $sort);

        self::assertEquals('a', $objects[0]->getFoo());
        self::assertEquals('b', $objects[1]->getFoo());
        self::assertEquals('c', $objects[2]->getFoo());
    }

    public function testSortByFieldDescending() : void
    {
        $objects = [new TestObject('b'), new TestObject('a'), new TestObject('c')];
        $sort    = ClosureExpressionVisitor::sortByField('foo', -1);

        usort($objects, $sort);

        self::assertEquals('c', $objects[0]->getFoo());
        self::assertEquals('b', $objects[1]->getFoo());
        self::assertEquals('a', $objects[2]->getFoo());
    }

    public function testSortDelegate() : void
    {
        $objects = [new TestObject('a', 'c'), new TestObject('a', 'b'), new TestObject('a', 'a')];
        $sort    = ClosureExpressionVisitor::sortByField('bar', 1);
        $sort    = ClosureExpressionVisitor::sortByField('foo', 1, $sort);

        usort($objects, $sort);

        self::assertEquals('a', $objects[0]->getBar());
        self::assertEquals('b', $objects[1]->getBar());
        self::assertEquals('c', $objects[2]->getBar());
    }

    public function testArrayComparison() : void
    {
        $closure = $this->visitor->walkComparison($this->builder->eq('foo', 42));

        self::assertTrue($closure(['foo' => 42]));
    }
}

class TestObject
{
    /** @var mixed */
    private $foo;

    /** @var mixed */
    private $bar;

    /** @var mixed */
    private $baz;

    /** @var mixed */
    private $qux;

    public function __construct($foo = null, $bar = null, $baz = null, $qux = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
        $this->qux = $qux;
    }

    public function __call(string $name, array $arguments)
    {
        if ($name === 'getqux') {
            return $this->qux;
        }
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function isBaz()
    {
        return $this->baz;
    }
}

class TestObjectNotCamelCase
{
    /** @var int|null */
    private $foo_bar;

    public function __construct(?int $foo_bar)
    {
        $this->foo_bar = $foo_bar;
    }

    public function getFooBar()
    {
        return $this->foo_bar;
    }
}

class TestObjectBothCamelCaseAndUnderscore
{
    /** @var int|null */
    private $foo_bar;

    /** @var int|null */
    private $fooBar;

    public function __construct(?int $foo_bar = null, ?int $fooBar = null)
    {
        $this->foo_bar = $foo_bar;
        $this->fooBar  = $fooBar;
    }

    public function getFooBar() : ?int
    {
        return $this->fooBar;
    }
}

class TestObjectPublicCamelCaseAndPrivateUnderscore
{
    /** @var int|null */
    private $foo_bar;

    /** @var int|null */
    public $fooBar;

    public function __construct(?int $foo_bar = null, ?int $fooBar = null)
    {
        $this->foo_bar = $foo_bar;
        $this->fooBar  = $fooBar;
    }

    public function getFooBar() : ?int
    {
        return $this->fooBar;
    }
}

class TestObjectBothPublic
{
    /** @var mixed */
    public $foo_bar;
    /** @var mixed */
    public $fooBar;

    public function __construct($foo_bar = null, $fooBar = null)
    {
        $this->foo_bar = $foo_bar;
        $this->fooBar  = $fooBar;
    }

    public function getFooBar()
    {
        return $this->foo_bar;
    }
}
