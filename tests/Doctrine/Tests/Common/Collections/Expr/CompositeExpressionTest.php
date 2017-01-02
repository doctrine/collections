<?php

namespace Doctrine\Tests\Common\Collections\Expr;

use Doctrine\Common\Collections\Expr\CompositeExpression;
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
        return array(
            array(
                'expression' => new Value('value'),
            ),
            array(
                'expression' => 'wrong-type',
            ),
        );
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
        $expressions = array(
            $expression,
        );

        $this->setExpectedException('\RuntimeException');
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
        $type = CompositeExpression::TYPE_AND;
        $expressions = array(
            $this->createMock('Doctrine\Common\Collections\Expr\Expression'),
        );

        $compositeExpression = new CompositeExpression($type, $expressions);

        return $compositeExpression;
    }

    /**
     * @return void
     */
    public function testGetExpressionList()
    {
        $compositeExpression = $this->createCompositeExpression();

        $expectedExpressionList = array(
            $this->createMock('Doctrine\Common\Collections\Expr\Expression'),
        );
        $actualExpressionList = $compositeExpression->getExpressionList();

        $this->assertEquals($expectedExpressionList, $actualExpressionList);
    }

    /**
     * @return void
     */
    public function testVisitor()
    {
        $compositeExpression = $this->createCompositeExpression();

        $visitor = $this->getMockForAbstractClass('Doctrine\Common\Collections\Expr\ExpressionVisitor');
        $visitor
            ->expects($this->once())
            ->method('walkCompositeExpression');

        /** @var ExpressionVisitor $visitor */
        $compositeExpression->visit($visitor);
    }
}