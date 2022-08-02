<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

class Value implements Expression
{
    public function __construct(private readonly mixed $value)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function visit(ExpressionVisitor $visitor): mixed
    {
        return $visitor->walkValue($this);
    }
}
