<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    use VerifyDeprecations;

    public function testCreate(): void
    {
        $criteria = Criteria::create();

        self::assertInstanceOf(Criteria::class, $criteria);
    }

    public function testConstructor(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria($expr, ['foo' => 'ASC'], 10, 20);

        self::assertSame($expr, $criteria->getWhereExpression());
        self::assertSame(['foo' => 'ASC'], $criteria->getOrderings());
        self::assertSame(10, $criteria->getFirstResult());
        self::assertSame(20, $criteria->getMaxResults());
    }

    public function testDeprecatedNullOffset(): void
    {
        $expr = new Comparison('field', '=', 'value');

        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/311');
        $criteria = new Criteria($expr, ['foo' => 'ASC'], null, 20);

        self::assertSame($expr, $criteria->getWhereExpression());
        self::assertSame(['foo' => 'ASC'], $criteria->getOrderings());
        self::assertNull($criteria->getFirstResult());
        self::assertSame(20, $criteria->getMaxResults());
    }

    public function testDefaultConstructor(): void
    {
        $this->expectNoDeprecationWithIdentifier('https://github.com/doctrine/collections/pull/311');
        $criteria = new Criteria();

        self::assertNull($criteria->getWhereExpression());
        self::assertSame([], $criteria->getOrderings());
        self::assertNull($criteria->getFirstResult());
        self::assertNull($criteria->getMaxResults());
    }

    public function testWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria();

        $criteria->where($expr);

        self::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testAndWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->andWhere($expr);

        $where = $criteria->getWhereExpression();
        self::assertInstanceOf(CompositeExpression::class, $where);

        self::assertEquals(CompositeExpression::TYPE_AND, $where->getType());
        self::assertSame([$expr, $expr], $where->getExpressionList());
    }

    public function testAndWhereWithoutWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria();

        $criteria->andWhere($expr);

        self::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->orWhere($expr);

        $where = $criteria->getWhereExpression();
        self::assertInstanceOf(CompositeExpression::class, $where);

        self::assertEquals(CompositeExpression::TYPE_OR, $where->getType());
        self::assertSame([$expr, $expr], $where->getExpressionList());
    }

    public function testOrWhereWithoutWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria();

        $criteria->orWhere($expr);

        self::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrderings(): void
    {
        $criteria = Criteria::create()
            ->orderBy(['foo' => 'ASC']);

        self::assertEquals(['foo' => 'ASC'], $criteria->getOrderings());
    }

    public function testExpr(): void
    {
        self::assertInstanceOf(ExpressionBuilder::class, Criteria::expr());
    }
}
