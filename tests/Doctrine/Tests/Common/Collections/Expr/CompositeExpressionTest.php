<?php

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @author  Tobias Oberrauch <hello@tobiasoberrauch.com>
 * @covers  \Doctrine\Common\Collections\Expr\CompositeExpression
 */
class CompositeExpressionTest extends TestCase
{
    /**
     * @return array
     */
    public function invalidDataProvider()
    {
        return [
            [
                'expression' => new Value('value'),
            ],
            [
                'expression' => 'wrong-type',
            ],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param $expression
     * @return void
     */
    public function testExceptions($expression)
    {
        $type = CompositeExpression::TYPE_AND;
        $expressions = [
            $expression,
        ];

        $this->expectException(\RuntimeException::class);
        new CompositeExpression($type, $expressions);
    }

    /**
     * @return void
     */
    public function testGetType()
    {
        $compositeExpression = $this->createCompositeExpression();

        $expectedType = CompositeExpression::TYPE_AND;
        $actualType = $compositeExpression->getType();

        $this->assertSame($expectedType, $actualType);
    }

    /**
     * @return CompositeExpression
     */
    protected function createCompositeExpression()
    {
        $type        = CompositeExpression::TYPE_AND;
        $expressions = [$this->createMock(Expression::class)];

        $compositeExpression = new CompositeExpression($type, $expressions);

        return $compositeExpression;
    }

    /**
     * @return void
     */
    public function testGetExpressionList()
    {
        $compositeExpression    = $this->createCompositeExpression();
        $expectedExpressionList = [$this->createMock(Expression::class)];
        $actualExpressionList   = $compositeExpression->getExpressionList();

        $this->assertEquals($expectedExpressionList, $actualExpressionList);
    }

    /**
     * @return void
     */
    public function testVisitor()
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
