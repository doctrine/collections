<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

/**
 * An Expression visitor walks a graph of expressions and turns them into a
 * query for the underlying implementation.
 */
abstract class ExpressionVisitor
{
    /**
     * Converts a comparison expression into the target query language output.
     *
     * @return mixed
     */
    abstract public function walkComparison(Comparison $comparison);

    /**
     * Converts a value expression into the target query language part.
     *
     * @return mixed
     */
    abstract public function walkValue(Value $value);

    /**
     * Converts a composite expression into the target query language output.
     *
     * @return mixed
     */
    abstract public function walkCompositeExpression(CompositeExpression $expr);

    /**
     * Dispatches walking an expression to the appropriate handler.
     *
     * @return mixed
     */
    public function dispatch(Expression $expr)
    {
        return $expr->visit($this);
    }
}
