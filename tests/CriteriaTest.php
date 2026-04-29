<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\ExpressionBuilder;
use PHPUnit\Framework\TestCase;
use SortDirection;

class CriteriaTest extends TestCase
{
    public function testCreate(): void
    {
        $criteria = Criteria::create();

        self::assertInstanceOf(Criteria::class, $criteria);
    }

    public function testConstructor(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = new Criteria($expr, ['foo' => SortDirection::Ascending], 10, 20);

        self::assertSame($expr, $criteria->getWhereExpression());
        self::assertSame(['foo' => SortDirection::Ascending], $criteria->getOrderings());
        self::assertSame(10, $criteria->getFirstResult());
        self::assertSame(20, $criteria->getMaxResults());
    }

    public function testWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = Criteria::create();

        $criteria->where($expr);

        self::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testAndWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = Criteria::create();

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
        $criteria = Criteria::create();

        $criteria->andWhere($expr);

        self::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrWhere(): void
    {
        $expr     = new Comparison('field', '=', 'value');
        $criteria = Criteria::create();

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
        $criteria = Criteria::create();

        $criteria->orWhere($expr);

        self::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testExpr(): void
    {
        self::assertInstanceOf(ExpressionBuilder::class, Criteria::expr());
    }

    public function testGetOrderings(): void
    {
        $criteria = Criteria::create()
            ->orderBy(['foo' => SortDirection::Ascending, 'bar' => SortDirection::Descending]);

        self::assertEquals([
            'foo' => SortDirection::Ascending,
            'bar' => SortDirection::Descending,
        ], $criteria->getOrderings());
    }
}
