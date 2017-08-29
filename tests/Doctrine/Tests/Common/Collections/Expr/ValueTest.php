<?php

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @author  Tobias Oberrauch <hello@tobiasoberrauch.com>
 * @covers  \Doctrine\Common\Collections\Expr\Value
 */
class ValueTest extends TestCase
{
    public function testGetter() : void
    {
        $value = 'foo';
        $valueExpression = new Value($value);

        $actualValue = $valueExpression->getValue();

        self::assertEquals($value, $actualValue);
    }

    public function testVisitor() : void
    {
        $visitor = $this->getMockForAbstractClass(ExpressionVisitor::class);
        $visitor
            ->expects($this->once())
            ->method('walkValue');

        /** @var ExpressionVisitor $visitor */
        $value = 'foo';
        $valueExpression = new Value($value);
        $valueExpression->visit($visitor);
    }
}
