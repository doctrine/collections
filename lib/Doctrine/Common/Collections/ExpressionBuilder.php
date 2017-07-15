<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Value;
use function func_get_args;

/**
 * Builder for Expressions in the {@link Selectable} interface.
 *
 * Important Notice for interoperable code: You have to use scalar
 * values only for comparisons, otherwise the behavior of the comparison
 * may be different between implementations (Array vs ORM vs ODM).
 */
class ExpressionBuilder
{
    /**
     * @param mixed $x
     *
     * @return CompositeExpression
     */
    public function andX($x = null): CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
    }

    /**
     * @param mixed $x
     *
     * @return CompositeExpression
     */
    public function orX($x = null): CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function eq(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::EQ, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function gt(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::GT, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function lt($field, $value)
    {
        return new Comparison($field, Comparison::LT, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function gte(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::GTE, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function lte(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::LTE, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function neq(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::NEQ, new Value($value));
    }

    /**
     * @param string $field
     *
     * @return Comparison
     */
    public function isNull(string $field): Comparison
    {
        return new Comparison($field, Comparison::EQ, new Value(null));
    }

    /**
     * @param string $field
     * @param array  $values
     *
     * @return Comparison
     */
    public function in(string $field, array $values): Comparison
    {
        return new Comparison($field, Comparison::IN, new Value($values));
    }

    /**
     * @param string $field
     * @param array  $values
     *
     * @return Comparison
     */
    public function notIn(string $field, array $values): Comparison
    {
        return new Comparison($field, Comparison::NIN, new Value($values));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function contains(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::CONTAINS, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function memberOf(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::MEMBER_OF, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function startsWith(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::STARTS_WITH, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function endsWith(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::ENDS_WITH, new Value($value));
    }
}
