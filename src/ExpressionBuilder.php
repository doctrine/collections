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
    /** @return CompositeExpression */
    public function andX(Expression ...$expressions)
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, $expressions);
    }

    /** @return CompositeExpression */
    public function orX(Expression ...$expressions)
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, $expressions);
    }

    public function not(Expression $expression): CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_NOT, [$expression]);
    }

    /** @return Comparison */
    public function eq(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::EQ, new Value($value));
    }

    /** @return Comparison */
    public function gt(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::GT, new Value($value));
    }

    /** @return Comparison */
    public function lt(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::LT, new Value($value));
    }

    /** @return Comparison */
    public function gte(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::GTE, new Value($value));
    }

    /** @return Comparison */
    public function lte(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::LTE, new Value($value));
    }

    /** @return Comparison */
    public function neq(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::NEQ, new Value($value));
    }

    /** @return Comparison */
    public function isNull(string $field)
    {
        return new Comparison($field, Comparison::EQ, new Value(null));
    }

    public function isNotNull(string $field): Comparison
    {
        return new Comparison($field, Comparison::NEQ, new Value(null));
    }

    /**
     * @param mixed[] $values
     *
     * @return Comparison
     */
    public function in(string $field, array $values)
    {
        return new Comparison($field, Comparison::IN, new Value($values));
    }

    /**
     * @param mixed[] $values
     *
     * @return Comparison
     */
    public function notIn(string $field, array $values)
    {
        return new Comparison($field, Comparison::NIN, new Value($values));
    }

    /** @return Comparison */
    public function contains(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::CONTAINS, new Value($value));
    }

    /** @return Comparison */
    public function memberOf(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::MEMBER_OF, new Value($value));
    }

    /** @return Comparison */
    public function startsWith(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::STARTS_WITH, new Value($value));
    }

    /** @return Comparison */
    public function endsWith(string $field, mixed $value)
    {
        return new Comparison($field, Comparison::ENDS_WITH, new Value($value));
    }
}
