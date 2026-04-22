<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Deprecated;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Deprecations\Deprecation;
use SortDirection;

use function func_num_args;

/**
 * Criteria for filtering Selectable collections.
 *
 * @phpstan-consistent-constructor
 */
final class Criteria
{
    private static ExpressionBuilder|null $expressionBuilder = null;

    /** @var array<string, Order|SortDirection> */
    private array $orderings = [];

    private int|null $firstResult = null;
    private int|null $maxResults  = null;

    /**
     * Creates an instance of the class.
     */
    public static function create(): static
    {
        if (func_num_args() === 1) {
            Deprecation::trigger(
                'doctrine/collections',
                'https://github.com/doctrine/collections/pull/486',
                'The `accessRawFieldValues` parameter passed to %s is deprecated and a no-op. You can remove it.',
                __METHOD__,
            );
        }

        return new static();
    }

    /**
     * Returns the expression builder.
     */
    public static function expr(): ExpressionBuilder
    {
        if (self::$expressionBuilder === null) {
            self::$expressionBuilder = new ExpressionBuilder();
        }

        return self::$expressionBuilder;
    }

    /**
     * Construct a new Criteria.
     *
     * @param array<string, Order|SortDirection>|null $orderings
     */
    public function __construct(
        private Expression|null $expression = null,
        array|null $orderings = null,
        int $firstResult = 0,
        int|null $maxResults = null,
    ) {
        if (func_num_args() === 5) {
            Deprecation::trigger(
                'doctrine/collections',
                'https://github.com/doctrine/collections/pull/486',
                'The `accessRawFieldValues` parameter passed to %s is deprecated and a no-op. You can remove it.',
                __METHOD__,
            );
        }

        $this->setFirstResult($firstResult);
        $this->setMaxResults($maxResults);

        if ($orderings === null) {
            return;
        }

        $this->orderBy($orderings);
    }

    /**
     * Sets the where expression to evaluate when this Criteria is searched for.
     *
     * @return $this
     */
    public function where(Expression $expression): static
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Appends the where expression to evaluate when this Criteria is searched for
     * using an AND with previous expression.
     *
     * @return $this
     */
    public function andWhere(Expression $expression): static
    {
        if ($this->expression === null) {
            return $this->where($expression);
        }

        $this->expression = new CompositeExpression(
            CompositeExpression::TYPE_AND,
            [$this->expression, $expression],
        );

        return $this;
    }

    /**
     * Appends the where expression to evaluate when this Criteria is searched for
     * using an OR with previous expression.
     *
     * @return $this
     */
    public function orWhere(Expression $expression): static
    {
        if ($this->expression === null) {
            return $this->where($expression);
        }

        $this->expression = new CompositeExpression(
            CompositeExpression::TYPE_OR,
            [$this->expression, $expression],
        );

        return $this;
    }

    /**
     * Gets the expression attached to this Criteria.
     */
    public function getWhereExpression(): Expression|null
    {
        return $this->expression;
    }

    /**
     * Gets the current orderings of this Criteria.
     *
     * @return array<string, SortDirection>
     */
    public function getOrderings(): array
    {
        return array_map(
            static fn (Order|SortDirection $order): SortDirection => $order instanceof Order
                ? $order == Order::Ascending
                    ? SortDirection::Ascending
                    : SortDirection::Descending
                : $order,
            $this->orderings,
        );
    }

    /**
     * Gets the current orderings of this Criteria.
     *
     * @return array<string, Order>
     */
    #[Deprecated(message: 'Use getOrderings() instead.', since: 'doctrine/collections 3.1')]
    public function orderings(): array
    {
        return array_map(
            static fn (Order|SortDirection $order): Order => $order instanceof SortDirection
                ? $order == SortDirection::Ascending
                    ? Order::Ascending
                    : Order::Descending
                : $order,
            $this->orderings,
        );
    }

    /**
     * Sets the ordering of the result of this Criteria.
     *
     * Keys are field and values are the order, being a valid SortDirection enum case.
     *
     * @see SortDirection::Ascending
     * @see SortDirection::Descending
     *
     * @param array<string, Order|SortDirection> $orderings
     *
     * @return $this
     */
    public function orderBy(array $orderings): static
    {
        $this->orderings = $orderings;

        return $this;
    }

    /**
     * Gets the current first result option of this Criteria.
     */
    public function getFirstResult(): int|null
    {
        return $this->firstResult;
    }

    /**
     * Set the number of first result that this Criteria should return.
     *
     * @param int $firstResult The value to set.
     *
     * @return $this
     */
    public function setFirstResult(int $firstResult): static
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * Gets maxResults.
     */
    public function getMaxResults(): int|null
    {
        return $this->maxResults;
    }

    /**
     * Sets maxResults.
     *
     * @param int|null $maxResults The value to set.
     *
     * @return $this
     */
    public function setMaxResults(int|null $maxResults): static
    {
        $this->maxResults = $maxResults;

        return $this;
    }
}
