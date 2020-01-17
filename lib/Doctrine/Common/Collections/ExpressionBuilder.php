<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\Value;

/**
 * Builder for Expressions in the {@link Selectable} interface.
 *
 * Important Notice for interoperable code: You have to use scalar
 * values only for comparisons, otherwise the behavior of the comparison
 * may be different between implementations (Array vs ORM vs ODM).
 */
class ExpressionBuilder
{
    public function andX(Expression ...$expressions) : CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, $expressions);
    }

    public function orX(Expression ...$expressions) : CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, $expressions);
    }

    /**
     * @param mixed $value
     */
    public function eq(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::EQ, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function gt(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::GT, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function lt(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::LT, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function gte(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::GTE, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function lte(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::LTE, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function neq(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::NEQ, new Value($value));
    }

    public function isNull(string $field) : Comparison
    {
        return new Comparison($field, Comparison::EQ, new Value(null));
    }

    /**
     * @param array $values
     */
    public function in(string $field, array $values) : Comparison
    {
        return new Comparison($field, Comparison::IN, new Value($values));
    }

    /**
     * @param array $values
     */
    public function notIn(string $field, array $values) : Comparison
    {
        return new Comparison($field, Comparison::NIN, new Value($values));
    }

    /**
     * @param mixed $value
     */
    public function contains(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::CONTAINS, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function memberOf(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::MEMBER_OF, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function startsWith(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::STARTS_WITH, new Value($value));
    }

    /**
     * @param mixed $value
     */
    public function endsWith(string $field, $value) : Comparison
    {
        return new Comparison($field, Comparison::ENDS_WITH, new Value($value));
    }
}
