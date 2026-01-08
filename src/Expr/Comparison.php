<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use Override;

/**
 * Comparison of a field with a value by the given operator.
 */
final readonly class Comparison implements Expression
{
    public const string EQ          = '=';
    public const string NEQ         = '<>';
    public const string LT          = '<';
    public const string LTE         = '<=';
    public const string GT          = '>';
    public const string GTE         = '>=';
    public const string IS          = '='; // no difference with EQ
    public const string IN          = 'IN';
    public const string NIN         = 'NIN';
    public const string CONTAINS    = 'CONTAINS';
    public const string MEMBER_OF   = 'MEMBER_OF';
    public const string STARTS_WITH = 'STARTS_WITH';
    public const string ENDS_WITH   = 'ENDS_WITH';

    private Value $value;

    public function __construct(private string $field, private string $op, mixed $value)
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

    #[Override]
    public function visit(ExpressionVisitor $visitor): mixed
    {
        return $visitor->walkComparison($this);
    }
}
