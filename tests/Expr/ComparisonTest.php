<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Comparison::class)]
class ComparisonTest extends TestCase
{
    public function testVisit(): void
    {
        $callableExpected = static function (): void {
        };

        $comparison = new Comparison('foo', Comparison::EQ, 3);

        $visitor = self::createMock(ExpressionVisitor::class);
        $visitor->expects(self::once())
            ->method('walkComparison')
            ->with($comparison)
            ->willReturn($callableExpected);

        self::assertSame($callableExpected, $comparison->visit($visitor));
    }
}
