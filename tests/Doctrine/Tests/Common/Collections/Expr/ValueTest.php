<?php

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @author  Tobias Oberrauch <hello@tobiasoberrauch.com>
 * @covers  \Doctrine\Common\Collections\Expr\Value
 */
class ValueTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetter()
    {
        $value = 'foo';
        $valueExpression = new Value($value);

        $actualValue = $valueExpression->getValue();

        $this->assertEquals($value, $actualValue);
    }

    /**
     * @return void
     */
    public function testVisitor()
    {
        $visitor = $this->getMockForAbstractClass('Doctrine\Common\Collections\Expr\ExpressionVisitor');
        $visitor
            ->expects($this->once())
            ->method('walkValue');

        /** @var ExpressionVisitor $visitor */
        $value = 'foo';
        $valueExpression = new Value($value);
        $valueExpression->visit($visitor);
    }
}