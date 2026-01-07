<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

/**
 * Comparison of a field with a value by the given operator.
 *
 * @final since 2.5
 */
class Comparison implements Expression
{
    final public const string EQ          = '=';
    final public const string NEQ         = '<>';
    final public const string LT          = '<';
    final public const string LTE         = '<=';
    final public const string GT          = '>';
    final public const string GTE         = '>=';
    final public const string IS          = '='; // no difference with EQ
    final public const string IN          = 'IN';
    final public const string NIN         = 'NIN';
    final public const string CONTAINS    = 'CONTAINS';
    final public const string MEMBER_OF   = 'MEMBER_OF';
    final public const string STARTS_WITH = 'STARTS_WITH';
    final public const string ENDS_WITH   = 'ENDS_WITH';

    private readonly Value $value;

    public function __construct(private readonly string $field, private readonly string $op, mixed $value)
    {
        if (! ($value instanceof Value)) {
            $value = new Value($value);
        }

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
