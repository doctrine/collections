<?php

namespace Doctrine\Collections\Expr;

/**
 * Expression for the {@link Selectable} interface.
 */
interface Expression
{
    /**
     * @return mixed
     */
    public function visit(ExpressionVisitor $visitor);
}
