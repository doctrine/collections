<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

/**
 * Comparison of a field with a value by the given operator.
 */
class Comparison implements Expression
{
    public const EQ          = '=';
    public const NEQ         = '<>';
    public const LT          = '<';
    public const LTE         = '<=';
    public const GT          = '>';
    public const GTE         = '>=';
    public const IS          = '='; // no difference with EQ
    public const IN          = 'IN';
    public const NIN         = 'NIN';
    public const CONTAINS    = 'CONTAINS';
    public const MEMBER_OF   = 'MEMBER_OF';
    public const STARTS_WITH = 'STARTS_WITH';
    public const ENDS_WITH   = 'ENDS_WITH';

    private string $field;

    private string $op;

    private Value $value;

    public function __construct(string $field, string $operator, mixed $value)
    {
        if (! ($value instanceof Value)) {
            $value = new Value($value);
        }

        $this->field = $field;
        $this->op    = $operator;
        $this->value = $value;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): Value
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->op;
    }

    public function visit(ExpressionVisitor $visitor): mixed
    {
        return $visitor->walkComparison($this);
    }
}
