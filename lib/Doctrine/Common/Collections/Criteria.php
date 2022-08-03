<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;

use function array_map;
use function strtoupper;

/**
 * Criteria for filtering Selectable collections.
 *
 * @psalm-consistent-constructor
 */
class Criteria
{
    public const ASC  = 'ASC';
    public const DESC = 'DESC';

    private static ?ExpressionBuilder $expressionBuilder = null;

    private ?Expression $expression = null;

    /** @var array<string, string> */
    private array $orderings = [];

    private ?int $firstResult = null;

    private ?int $maxResults = null;

    /**
     * Creates an instance of the class.
     *
     * @return Criteria
     */
    public static function create(): self
    {
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
     * @param array<string, string>|null $orderings
     */
    public function __construct(?Expression $expression = null, ?array $orderings = null, ?int $firstResult = null, ?int $maxResults = null)
    {
        $this->expression = $expression;

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
    public function where(Expression $expression): self
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
    public function andWhere(Expression $expression): self
    {
        if ($this->expression === null) {
            return $this->where($expression);
        }

        $this->expression = new CompositeExpression(
            CompositeExpression::TYPE_AND,
            [$this->expression, $expression]
        );

        return $this;
    }

    /**
     * Appends the where expression to evaluate when this Criteria is searched for
     * using an OR with previous expression.
     *
     * @return $this
     */
    public function orWhere(Expression $expression): self
    {
        if ($this->expression === null) {
            return $this->where($expression);
        }

        $this->expression = new CompositeExpression(
            CompositeExpression::TYPE_OR,
            [$this->expression, $expression]
        );

        return $this;
    }

    /**
     * Gets the expression attached to this Criteria.
     */
    public function getWhereExpression(): ?Expression
    {
        return $this->expression;
    }

    /**
     * Gets the current orderings of this Criteria.
     *
     * @return array<string, string>
     */
    public function getOrderings(): array
    {
        return $this->orderings;
    }

    /**
     * Sets the ordering of the result of this Criteria.
     *
     * Keys are field and values are the order, being either ASC or DESC.
     *
     * @see Criteria::ASC
     * @see Criteria::DESC
     *
     * @param array<string, string> $orderings
     *
     * @return $this
     */
    public function orderBy(array $orderings): self
    {
        $this->orderings = array_map(
            static function (string $ordering): string {
                return strtoupper($ordering) === Criteria::ASC ? Criteria::ASC : Criteria::DESC;
            },
            $orderings
        );

        return $this;
    }

    /**
     * Gets the current first result option of this Criteria.
     */
    public function getFirstResult(): ?int
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
    public function setFirstResult(?int $firstResult): self
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * Gets maxResults.
     */
    public function getMaxResults(): ?int
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
    public function setMaxResults(?int $maxResults): self
    {
        $this->maxResults = $maxResults;

        return $this;
    }
}
