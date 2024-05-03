<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Deprecations\Deprecation;

use function array_map;
use function func_num_args;
use function strtoupper;

/**
 * Criteria for filtering Selectable collections.
 *
 * @psalm-consistent-constructor
 */
class Criteria
{
    final public const ASC  = 'ASC';
    final public const DESC = 'DESC';

    private static ExpressionBuilder|null $expressionBuilder = null;

    /** @var array<string, Order> */
    private array $orderings = [];

    private int|null $firstResult = null;
    private int|null $maxResults  = null;

    /**
     * Creates an instance of the class.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Returns the expression builder.
     *
     * @return ExpressionBuilder
     */
    public static function expr()
    {
        if (self::$expressionBuilder === null) {
            self::$expressionBuilder = new ExpressionBuilder();
        }

        return self::$expressionBuilder;
    }

    /**
     * Construct a new Criteria.
     *
     * @param array<string, string|Order>|null $orderings
     */
    public function __construct(
        private Expression|null $expression = null,
        array|null $orderings = null,
        int|null $firstResult = null,
        int|null $maxResults = null,
    ) {
        $this->expression = $expression;

        if ($firstResult === null && func_num_args() > 2) {
            Deprecation::trigger(
                'doctrine/collections',
                'https://github.com/doctrine/collections/pull/311',
                'Passing null as $firstResult to the constructor of %s is deprecated. Pass 0 instead or omit the argument.',
                self::class,
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
    public function where(Expression $expression)
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
    public function andWhere(Expression $expression)
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
    public function orWhere(Expression $expression)
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
     *
     * @return Expression|null
     */
    public function getWhereExpression()
    {
        return $this->expression;
    }

    /**
     * Gets the current orderings of this Criteria.
     *
     * @return array<string, string>
     */
    public function getOrderings()
    {
        return array_map(
            static fn (Order $ordering): string => $ordering->value,
            $this->orderings,
        );
    }

    /**
     * Gets the current orderings of this Criteria.
     *
     * @return array<string, Order>
     */
    public function orderings(): array
    {
        return $this->orderings;
    }

    /**
     * Sets the ordering of the result of this Criteria.
     *
     * Keys are field and values are the order, being a valid Order enum case.
     *
     * @see Order::Ascending
     * @see Order::Descending
     *
     * @param array<string, string|Order> $orderings
     *
     * @return $this
     */
    public function orderBy(array $orderings)
    {
        $method          = __METHOD__;
        $this->orderings = array_map(
            static function (string|Order $ordering): Order {
                if ($ordering instanceof Order) {
                    return $ordering;
                }

                return strtoupper($ordering) === Order::Ascending->value ? Order::Ascending : Order::Descending;
            },
            $orderings,
        );

        return $this;
    }

    /**
     * Gets the current first result option of this Criteria.
     *
     * @return int|null
     */
    public function getFirstResult()
    {
        return $this->firstResult;
    }

    /**
     * Set the number of first result that this Criteria should return.
     *
     * @param int|null $firstResult The value to set.
     *
     * @return $this
     */
    public function setFirstResult(int|null $firstResult)
    {
        if ($firstResult === null) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/collections',
                'https://github.com/doctrine/collections/pull/311',
                'Passing null to %s() is deprecated, pass 0 instead.',
                __METHOD__,
            );
        }

        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * Gets maxResults.
     *
     * @return int|null
     */
    public function getMaxResults()
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
    public function setMaxResults(int|null $maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }
}
