<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use Override;
use RuntimeException;

use function count;

/**
 * Expression of Expressions combined by AND or OR operation.
 *
 * @final since 2.5
 */
final readonly class CompositeExpression implements Expression
{
    final public const string TYPE_AND = 'AND';
    final public const string TYPE_OR  = 'OR';
    final public const string TYPE_NOT = 'NOT';

    /** @var list<Expression> */
    private array $expressions;

    /**
     * @param Expression[] $expressions
     *
     * @throws RuntimeException
     */
    public function __construct(private string $type, array $expressions)
    {
        $validatedExpressions = [];

        foreach ($expressions as $expr) {
            if ($expr instanceof Value) {
                throw new RuntimeException('Values are not supported expressions as children of and/or expressions.');
            }

            if (! ($expr instanceof Expression)) {
                throw new RuntimeException('No expression given to CompositeExpression.');
            }

            $validatedExpressions[] = $expr;
        }

        if ($type === self::TYPE_NOT && count($validatedExpressions) !== 1) {
            throw new RuntimeException('Not expression only allows one expression as child.');
        }

        $this->expressions = $validatedExpressions;
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

    #[Override]
    public function visit(ExpressionVisitor $visitor): mixed
    {
        return $visitor->walkCompositeExpression($this);
    }
}
