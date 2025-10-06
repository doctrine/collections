<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Value::class)]
class ValueTest extends TestCase
{
    public function testGetter(): void
    {
        $value           = 'foo';
        $valueExpression = new Value($value);

        $actualValue = $valueExpression->getValue();

        self::assertEquals($value, $actualValue);
    }

    public function testVisitor(): void
    {
        $visitor = $this->createMock(ExpressionVisitor::class);
        $visitor
            ->expects($this->once())
            ->method('walkValue');

        $value           = 'foo';
        $valueExpression = new Value($value);
        $valueExpression->visit($visitor);
    }
}
