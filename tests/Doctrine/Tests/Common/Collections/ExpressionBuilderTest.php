<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Composition;

/**
 * @group DDC-1637
 */
class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->builder = new ExpressionBuilder();
    }

    public function testAndX()
    {
        $expr = $this->builder->andX($this->builder->eq('a', 'b'));

        static::assertInstanceOf(Composition\AndComposition::class, $expr);
    }

    public function testOrX()
    {
        $expr = $this->builder->orX($this->builder->eq('a', 'b'));

        static::assertInstanceOf(Composition\OrComposition::class, $expr);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidAndXArgument()
    {
        $this->builder->andX('foo');
    }

    public function testEq()
    {
        $expr = $this->builder->eq('a', 'b');

        static::assertInstanceOf(Comparison\Equal::class, $expr);
    }

    public function testNeq()
    {
        $expr = $this->builder->neq('a', 'b');

        static::assertInstanceOf(Comparison\NotEqual::class, $expr);
    }

    public function testLt()
    {
        $expr = $this->builder->lt('a', 'b');

        static::assertInstanceOf(Comparison\LessThan::class, $expr);
    }

    public function testGt()
    {
        $expr = $this->builder->gt('a', 'b');

        static::assertInstanceOf(Comparison\GreaterThan::class, $expr);
    }

    public function testGte()
    {
        $expr = $this->builder->gte('a', 'b');

        static::assertInstanceOf(Comparison\GreaterThanEqual::class, $expr);
    }

    public function testLte()
    {
        $expr = $this->builder->lte('a', 'b');

        static::assertInstanceOf(Comparison\LessThanEqual::class, $expr);
    }

    public function testIn()
    {
        $expr = $this->builder->in('a', array('b'));

        static::assertInstanceOf(Comparison\In::class, $expr);
    }

    public function testNotIn()
    {
        $expr = $this->builder->notIn('a', array('b'));

        static::assertInstanceOf(Comparison\NotIn::class, $expr);
    }

    public function testIsNull()
    {
        $expr = $this->builder->isNull('a');

        static::assertInstanceOf(Comparison\Equal::class, $expr);
    }

    public function testContains()
    {
        $expr = $this->builder->contains('a', 'b');

        static::assertInstanceOf(Comparison\Contains::class, $expr);
    }

    public function testMemberOf()
    {
        $expr = $this->builder->memberOf('b', array('a'));

        static::assertInstanceOf(Comparison\MemberOf::class, $expr);
    }

    public function testStartsWith()
    {
        $expr = $this->builder->startsWith('a', 'b');

        static::assertInstanceOf(Comparison\StartsWith::class, $expr);
    }

    public function testEndsWith()
    {
        $expr = $this->builder->endsWith('a', 'b');

        static::assertInstanceOf(Comparison\EndsWith::class, $expr);
    }    
}
