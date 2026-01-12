<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use RuntimeException;

use function count;

/**
 * Expression of Expressions combined by AND or OR operation.
 *
 * @final since 2.5
 */
class CompositeExpression implements Expression
{
    final public const TYPE_AND = 'AND';
    final public const TYPE_OR  = 'OR';
    final public const TYPE_NOT = 'NOT';

    /** @var list<Expression> */
    private array $expressions = [];

    /**
     * @param Expression[] $expressions
     *
     * @throws RuntimeException
     */
    public function __construct(private readonly string $type, array $expressions)
    {
        foreach ($expressions as $expr) {
            if ($expr instanceof Value) {
                throw new RuntimeException('Values are not supported expressions as children of and/or expressions.');
            }

            if (! ($expr instanceof Expression)) {
                throw new RuntimeException('No expression given to CompositeExpression.');
            }

            $this->expressions[] = $expr;
        }

        if ($type === self::TYPE_NOT && count($this->expressions) !== 1) {
            throw new RuntimeException('Not expression only allows one expression as child.');
        }
    }

    /**
     * Returns the list of expressions nested in this composite.
     *
     * @return list<Expression>
     */
    public function getExpressionList()
    {
        return $this->expressions;
    }

    /** @return string */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function visit(ExpressionVisitor $visitor)
    {
        return $visitor->walkCompositeExpression($this);
    }
}
