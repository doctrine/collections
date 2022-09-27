<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

/**
 * Expression for the {@link Selectable} interface.
 */
interface Expression
{
    /** @return mixed */
    public function visit(ExpressionVisitor $visitor);
}
