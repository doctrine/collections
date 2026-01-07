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
    final public const EQ          = '=';
    final public const NEQ         = '<>';
    final public const LT          = '<';
    final public const LTE         = '<=';
    final public const GT          = '>';
    final public const GTE         = '>=';
    final public const IS          = '='; // no difference with EQ
    final public const IN          = 'IN';
    final public const NIN         = 'NIN';
    final public const CONTAINS    = 'CONTAINS';
    final public const MEMBER_OF   = 'MEMBER_OF';
    final public const STARTS_WITH = 'STARTS_WITH';
    final public const ENDS_WITH   = 'ENDS_WITH';

    private readonly Value $value;

    public function __construct(private readonly string $field, private readonly string $op, mixed $value)
    {
        if (! ($value instanceof Value)) {
            $value = new Value($value);
        }

        $this->value = $value;
    }

    /** @return string */
    public function getField()
    {
        return $this->field;
    }

    /** @return Value */
    public function getValue()
    {
        return $this->value;
    }

    /** @return string */
    public function getOperator()
    {
        return $this->op;
    }

    /**
     * {@inheritDoc}
     */
    public function visit(ExpressionVisitor $visitor)
    {
        return $visitor->walkComparison($this);
    }
}
