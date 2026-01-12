<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(CompositeExpression::class)]
class CompositeExpressionTest extends TestCase
{
    /** @return list<array{type:string, expressions: list<mixed>}> */
    public static function invalidDataProvider(): array
    {
        return [
            ['type' => CompositeExpression::TYPE_AND, 'expressions' => [new Value('value')]],
            ['type' => CompositeExpression::TYPE_AND, 'expressions' => ['wrong-type']],
            [
                'type' => CompositeExpression::TYPE_NOT,
                'expressions' => [
                    self::createStub(Expression::class),
                    self::createStub(Expression::class),
                ],
            ],
        ];
    }

    /** @param list<mixed> $expressions */
    #[DataProvider('invalidDataProvider')]
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
        $expressions = [$this->createStub(Expression::class)];

        return new CompositeExpression($type, $expressions);
    }

    public function testGetExpressionList(): void
    {
        $compositeExpression    = $this->createCompositeExpression();
        $expectedExpressionList = [$this->createStub(Expression::class)];
        $actualExpressionList   = $compositeExpression->getExpressionList();

        self::assertEquals($expectedExpressionList, $actualExpressionList);
    }

    public function testVisitor(): void
    {
        $compositeExpression = $this->createCompositeExpression();

        $visitor = $this->createMock(ExpressionVisitor::class);
        $visitor
            ->expects(self::once())
            ->method('walkCompositeExpression');

        $compositeExpression->visit($visitor);
    }
}
