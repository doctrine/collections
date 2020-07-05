<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Doctrine\Common\Collections\Expr\Comparison
 */
class ComparisonTest extends TestCase
{
    public function testGetter() : void
    {
        $field    = 'id';
        $operator = Comparison::EQ;
        $value    = 'foo';

        $comparisonExpression = new Comparison($field, $operator, $value);

        self::assertEquals($field, $comparisonExpression->getField());
        self::assertEquals($operator, $comparisonExpression->getOperator());
        self::assertEquals($value, $comparisonExpression->getValue()->getValue());

        $comparisonExpression = new Comparison($field, $operator, new Value($value));

        self::assertEquals($value, $comparisonExpression->getValue()->getValue());
    }

    public function testVisitor() : void
    {
        $visitor = $this->getMockForAbstractClass(ExpressionVisitor::class);
        $visitor
            ->expects($this->once())
            ->method('walkComparison');

        /** @var ExpressionVisitor $visitor */
        $field    = 'id';
        $operator = Comparison::EQ;
        $value    = 'foo';

        $comparisonExpression = new Comparison($field, $operator, $value);
        $comparisonExpression->visit($visitor);
    }
}
