<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\ExpressionBuilder;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate() : void
    {
        $criteria = Criteria::create();

        $this->assertInstanceOf(Criteria::class, $criteria);
    }

    public function testConstructor() : void
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria($expr, ["foo" => "ASC"], 10, 20);

        $this->assertSame($expr, $criteria->getWhereExpression());
        $this->assertEquals(["foo" => "ASC"], $criteria->getOrderings());
        $this->assertEquals(10, $criteria->getFirstResult());
        $this->assertEquals(20, $criteria->getMaxResults());
    }

    public function testWhere() : void
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);

        $this->assertSame($expr, $criteria->getWhereExpression());
    }

    public function testAndWhere() : void
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->andWhere($expr);

        $where = $criteria->getWhereExpression();
        $this->assertInstanceOf(CompositeExpression::class, $where);

        $this->assertEquals(CompositeExpression::TYPE_AND, $where->getType());
        $this->assertSame([$expr, $expr], $where->getExpressionList());
    }

    public function testAndWhereWithoutWhere() : void
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->andWhere($expr);

        $this->assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrWhere() : void
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->orWhere($expr);

        $where = $criteria->getWhereExpression();
        $this->assertInstanceOf(CompositeExpression::class, $where);

        $this->assertEquals(CompositeExpression::TYPE_OR, $where->getType());
        $this->assertSame([$expr, $expr], $where->getExpressionList());
    }

    public function testOrWhereWithoutWhere() : void
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->orWhere($expr);

        $this->assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrderings() : void
    {
        $criteria = Criteria::create()
            ->orderBy(["foo" => "ASC"]);

        $this->assertEquals(["foo" => "ASC"], $criteria->getOrderings());
    }

    public function testExpr() : void
    {
        $this->assertInstanceOf(ExpressionBuilder::class, Criteria::expr());
    }
}
