<?php

namespace Doctrine\Common\Collections\Expr;

/**
 * Comparison of a field with a value by the given operator.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since  2.3
 */
class Comparison implements Expression
{
    const EQ          = '=';
    const NEQ         = '<>';
    const LT          = '<';
    const LTE         = '<=';
    const GT          = '>';
    const GTE         = '>=';
    const IS          = '='; // no difference with EQ
    const IN          = 'IN';
    const NIN         = 'NIN';
    const CONTAINS    = 'CONTAINS';
    const MEMBER_OF   = 'MEMBER_OF';
    const STARTS_WITH = 'STARTS_WITH';
    const ENDS_WITH   = 'ENDS_WITH';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $op;

    /**
     * @var Value
     */
    private $value;

    /**
     * @param string $field
     * @param string $operator
     * @param mixed  $value
     */
    public function __construct($field, $operator, $value)
    {
        if ( ! ($value instanceof Value)) {
            $value = new Value($value);
        }

        $this->field = $field;
        $this->op    = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
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
