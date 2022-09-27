<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

class Value implements Expression
{
    public function __construct(private readonly mixed $value)
    {
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function visit(ExpressionVisitor $visitor)
    {
        return $visitor->walkValue($this);
    }
}
