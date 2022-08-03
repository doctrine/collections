<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use RuntimeException;

/**
 * An Expression visitor walks a graph of expressions and turns them into a
 * query for the underlying implementation.
 */
abstract class ExpressionVisitor
{
    /**
     * Converts a comparison expression into the target query language output.
     */
    abstract public function walkComparison(Comparison $comparison): mixed;

    /**
     * Converts a value expression into the target query language part.
     */
    abstract public function walkValue(Value $value): mixed;

    /**
     * Converts a composite expression into the target query language output.
     */
    abstract public function walkCompositeExpression(CompositeExpression $expr): mixed;

    /**
     * Dispatches walking an expression to the appropriate handler.
     *
     * @throws RuntimeException
     */
    public function dispatch(Expression $expr): mixed
    {
        switch (true) {
            case $expr instanceof Comparison:
                return $this->walkComparison($expr);

            case $expr instanceof Value:
                return $this->walkValue($expr);

            case $expr instanceof CompositeExpression:
                return $this->walkCompositeExpression($expr);

            default:
                throw new RuntimeException('Unknown Expression ' . $expr::class);
        }
    }
}
