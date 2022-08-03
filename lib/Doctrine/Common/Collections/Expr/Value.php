<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

class Value implements Expression
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
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
