<?php

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @author  Tobias Oberrauch <hello@tobiasoberrauch.com>
 * @covers  \Doctrine\Common\Collections\Expr\CompositeExpression
 */
class CompositeExpressionTest extends TestCase
{
    /**
     * @return CompositeExpression
     */
    public function testConstructor()
    {
        $type = CompositeExpression::TYPE_AND;
        $expressions = array(
            $this->getMock('Doctrine\Common\Collections\Expr\Expression'),
        );

        return new CompositeExpression($type, $expressions);
    }

    /**
     * @depends testConstructor
     *
     * @param CompositeExpression $compositeExpression The given expression.
     */
    public function testGetType(CompositeExpression $compositeExpression)
    {
        $expectedType = CompositeExpression::TYPE_AND;
        $actualType = $compositeExpression->getType();

        $this->assertSame($expectedType, $actualType);
    }

    /**
     * @depends testConstructor
     *
     * @param CompositeExpression $compositeExpression The given expression.
     */
    public function testGetExpressionList(CompositeExpression $compositeExpression)
    {
        $expectedExpressionList = array(
            $this->getMock('Doctrine\Common\Collections\Expr\Expression'),
        );
        $actualExpressionList = $compositeExpression->getExpressionList();

        $this->assertEquals($expectedExpressionList, $actualExpressionList);
    }

    /**
     * @depends testConstructor
     *
     * @param CompositeExpression $compositeExpression The given expression.
     */
    public function testVisitor(CompositeExpression $compositeExpression)
    {
        $visitor = $this->getMockForAbstractClass('Doctrine\Common\Collections\Expr\ExpressionVisitor');
        $visitor
            ->expects($this->once())
            ->method('walkCompositeExpression');

        /** @var ExpressionVisitor $visitor */
        $compositeExpression->visit($visitor);
    }
}