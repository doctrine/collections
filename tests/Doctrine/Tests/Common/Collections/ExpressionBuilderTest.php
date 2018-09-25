<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\ExpressionBuilder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @group DDC-1637
 */
class ExpressionBuilderTest extends TestCase
{
    /** @var ExpressionBuilder */
    private $builder;

    protected function setUp() : void
    {
        $this->builder = new ExpressionBuilder();
    }

    public function testAndX() : void
    {
        $expr = $this->builder->andX($this->builder->eq('a', 'b'));

        self::assertInstanceOf(CompositeExpression::class, $expr);
        self::assertEquals(CompositeExpression::TYPE_AND, $expr->getType());
    }

    public function testOrX() : void
    {
        $expr = $this->builder->orX($this->builder->eq('a', 'b'));

        self::assertInstanceOf(CompositeExpression::class, $expr);
        self::assertEquals(CompositeExpression::TYPE_OR, $expr->getType());
    }

    public function testInvalidAndXArgument() : void
    {
        $this->expectException(RuntimeException::class);
        $this->builder->andX('foo');
    }

    public function testEq() : void
    {
        $expr = $this->builder->eq('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::EQ, $expr->getOperator());
    }

    public function testNeq() : void
    {
        $expr = $this->builder->neq('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::NEQ, $expr->getOperator());
    }

    public function testLt() : void
    {
        $expr = $this->builder->lt('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::LT, $expr->getOperator());
    }

    public function testGt() : void
    {
        $expr = $this->builder->gt('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::GT, $expr->getOperator());
    }

    public function testGte() : void
    {
        $expr = $this->builder->gte('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::GTE, $expr->getOperator());
    }

    public function testLte() : void
    {
        $expr = $this->builder->lte('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::LTE, $expr->getOperator());
    }

    public function testIn() : void
    {
        $expr = $this->builder->in('a', ['b']);

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::IN, $expr->getOperator());
    }

    public function testNotIn() : void
    {
        $expr = $this->builder->notIn('a', ['b']);

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::NIN, $expr->getOperator());
    }

    public function testIsNull() : void
    {
        $expr = $this->builder->isNull('a');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::EQ, $expr->getOperator());
    }

    public function testContains() : void
    {
        $expr = $this->builder->contains('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::CONTAINS, $expr->getOperator());
    }

    public function testMemberOf() : void
    {
        $expr = $this->builder->memberOf('b', ['a']);

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::MEMBER_OF, $expr->getOperator());
    }

    public function testStartsWith() : void
    {
        $expr = $this->builder->startsWith('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::STARTS_WITH, $expr->getOperator());
    }

    public function testEndsWith() : void
    {
        $expr = $this->builder->endsWith('a', 'b');

        self::assertInstanceOf(Comparison::class, $expr);
        self::assertEquals(Comparison::ENDS_WITH, $expr->getOperator());
    }
}
