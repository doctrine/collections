<?php

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers  \Doctrine\Common\Collections\Expr\CompositeExpression
 */
class CompositeExpressionTest extends TestCase
{
    public function invalidDataProvider() : array
    {
        return [
            ['expression' => new Value('value')],
            ['expression' => 'wrong-type'],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testExceptions($expression) : void
    {
        $type        = CompositeExpression::TYPE_AND;
        $expressions = [$expression];

        $this->expectException(RuntimeException::class);
        new CompositeExpression($type, $expressions);
    }

    public function testGetType() : void
    {
        $compositeExpression = $this->createCompositeExpression();

        $expectedType = CompositeExpression::TYPE_AND;
        $actualType   = $compositeExpression->getType();

        self::assertSame($expectedType, $actualType);
    }

    protected function createCompositeExpression() : CompositeExpression
    {
        $type        = CompositeExpression::TYPE_AND;
        $expressions = [$this->createMock(Expression::class)];

        return new CompositeExpression($type, $expressions);
    }

    public function testGetExpressionList() : void
    {
        $compositeExpression    = $this->createCompositeExpression();
        $expectedExpressionList = [$this->createMock(Expression::class)];
        $actualExpressionList   = $compositeExpression->getExpressionList();

        self::assertEquals($expectedExpressionList, $actualExpressionList);
    }

    public function testVisitor() : void
    {
        $compositeExpression = $this->createCompositeExpression();

        $visitor = $this->getMockForAbstractClass(ExpressionVisitor::class);
        $visitor
            ->expects($this->once())
            ->method('walkCompositeExpression');

        /** @var ExpressionVisitor $visitor */
        $compositeExpression->visit($visitor);
    }
}
