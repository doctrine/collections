<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use Override;

final readonly class Value implements Expression
{
    public function __construct(private mixed $value)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    #[Override]
    public function visit(ExpressionVisitor $visitor): mixed
    {
        return $visitor->walkValue($this);
    }
}
