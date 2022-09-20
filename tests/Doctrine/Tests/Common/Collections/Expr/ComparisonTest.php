<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Doctrine\Common\Collections\Expr\Comparison */
class ComparisonTest extends TestCase
{
    /** @covers ::visit */
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
