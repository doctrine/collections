<?php
namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria($expr, array("foo" => "ASC"), 10, 20);

        $this->assertSame($expr, $criteria->getWhereExpression());
        $this->assertEquals(array("foo" => "ASC"), $criteria->getOrderings());
        $this->assertEquals(10, $criteria->getFirstResult());
        $this->assertEquals(20, $criteria->getMaxResults());
    }

    public function testWhere()
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);

        $this->assertSame($expr, $criteria->getWhereExpression());
    }

    public function testOrderings()
    {
        $criteria = new Criteria();

        $criteria->orderBy(array("foo" => "ASC"));

        $this->assertEquals(array("foo" => "ASC"), $criteria->getOrderings());
    }
}
