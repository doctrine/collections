<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use RuntimeException;

/**
 * Expression of Expressions combined by AND or OR operation.
 */
class CompositeExpression implements Expression
{
    public const TYPE_AND = 'AND';
    public const TYPE_OR  = 'OR';

    /** @var string */
    private $type;

    /** @var list<Expression> */
    private $expressions = [];

    /**
     * @param Expression[] $expressions
     *
     * @throws RuntimeException
     */
    public function __construct(string $type, array $expressions)
    {
        $this->type = $type;

        foreach ($expressions as $expr) {
            if ($expr instanceof Value) {
                throw new RuntimeException('Values are not supported expressions as children of and/or expressions.');
            }

            if (! ($expr instanceof Expression)) {
                throw new RuntimeException('No expression given to CompositeExpression.');
            }

            $this->expressions[] = $expr;
        }
    }

    /**
     * Returns the list of expressions nested in this composite.
     *
     * @return list<Expression>
     */
    public function getExpressionList(): array
    {
        return $this->expressions;
    }

    public function getType(): string
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
