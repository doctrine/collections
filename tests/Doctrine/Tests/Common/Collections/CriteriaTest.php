<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Composition;
use Doctrine\Common\Collections\ExpressionBuilder;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $criteria = Criteria::create();

        static::assertInstanceOf(Criteria::class, $criteria);
    }

    public function testConstructor()
    {
        $expr     = new Comparison\Equal('field', 'value');
        $criteria = new Criteria($expr, array('foo' => 'ASC'), 10, 20);

        static::assertSame($expr, $criteria->getWhereExpression());
        static::assertEquals(array('foo' => 'ASC'), $criteria->getOrderings());
        static::assertEquals(10, $criteria->getFirstResult());
        static::assertEquals(20, $criteria->getMaxResults());
    }

    public function testWhere()
    {
        $expr     = new Comparison\Equal('field', 'value');
        $criteria = new Criteria();

        $criteria->where($expr);

        static::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testAndWhere()
    {
        $expr     = new Comparison\Equal('field', 'value');
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->andWhere($expr);

        $where = $criteria->getWhereExpression();
        static::assertInstanceOf(Composition\AndComposition::class, $where);
    }

    public function testAndWhereWithoutWhere()
    {
        $expr     = new Comparison\Equal('field', 'value');
        $criteria = new Criteria();

        $criteria->andWhere($expr);

        static::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrWhere()
    {
        $expr     = new Comparison\Equal('field', 'value');
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->orWhere($expr);

        $where = $criteria->getWhereExpression();
        static::assertInstanceOf(Composition\OrComposition::class, $where);
    }

    public function testOrWhereWithoutWhere()
    {
        $expr     = new Comparison\Equal('field', 'value');
        $criteria = new Criteria();

        $criteria->orWhere($expr);

        static::assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrderings()
    {
        $criteria = Criteria::create()
            ->orderBy(array('foo' => 'ASC'));

        static::assertEquals(array('foo' => 'ASC'), $criteria->getOrderings());
    }

    public function testExpr()
    {
        static::assertInstanceOf(ExpressionBuilder::class, Criteria::expr());
    }
}
