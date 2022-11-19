<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @covers  \Doctrine\Common\Collections\Expr\CompositeExpression */
class CompositeExpressionTest extends TestCase
{
    /** @return list<array{type:string, expressions: list<mixed>}> */
    public function invalidDataProvider(): array
    {
        return [
            ['type' => CompositeExpression::TYPE_AND, 'expressions' => [new Value('value')]],
            ['type' => CompositeExpression::TYPE_AND, 'expressions' => ['wrong-type']],
            ['type' => CompositeExpression::TYPE_NOT, 'expressions' => [$this->createMock(Expression::class), $this->createMock(Expression::class)]],
        ];
    }

    /**
     * @param list<mixed> $expressions
     *
     * @dataProvider invalidDataProvider
     */
    public function testExceptions(string $type, array $expressions): void
    {
        $this->expectException(RuntimeException::class);
        new CompositeExpression($type, $expressions);
    }

    public function testGetType(): void
    {
        $compositeExpression = $this->createCompositeExpression();

        $expectedType = CompositeExpression::TYPE_AND;
        $actualType   = $compositeExpression->getType();

        self::assertSame($expectedType, $actualType);
    }

    protected function createCompositeExpression(): CompositeExpression
    {
        $type        = CompositeExpression::TYPE_AND;
        $expressions = [$this->createMock(Expression::class)];

        return new CompositeExpression($type, $expressions);
    }

    public function testGetExpressionList(): void
    {
        $compositeExpression    = $this->createCompositeExpression();
        $expectedExpressionList = [$this->createMock(Expression::class)];
        $actualExpressionList   = $compositeExpression->getExpressionList();

        self::assertEquals($expectedExpressionList, $actualExpressionList);
    }

    public function testVisitor(): void
    {
        $compositeExpression = $this->createCompositeExpression();

        $visitor = $this->getMockForAbstractClass(ExpressionVisitor::class);
        $visitor
            ->expects($this->once())
            ->method('walkCompositeExpression');

        $compositeExpression->visit($visitor);
    }
}
