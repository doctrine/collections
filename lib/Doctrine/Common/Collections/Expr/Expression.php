<?php

namespace Doctrine\Common\Collections\Expr;

/**
 * Expression for the {@link Selectable} interface.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
interface Expression
{
    /**
     * @param ExpressionVisitor $visitor
     *
     * @return mixed
     */
    public function visit(ExpressionVisitor $visitor);
}
