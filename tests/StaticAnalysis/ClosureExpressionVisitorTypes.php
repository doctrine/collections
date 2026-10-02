<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections\StaticAnalysis;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use stdClass;

use function array_filter;

/**
 * Guards the types handed out by the closure expression visitor: the closures it
 * produces all accept objects as well as maps.
 */
final class ClosureExpressionVisitorTypes
{
    /**
     * @param list<array<string, mixed>> $elements
     */
    public function filter(ClosureExpressionVisitor $visitor, array $elements): void
    {
        $filter = $visitor->dispatch(new Comparison('foo', Comparison::EQ, 1));

        $filter(new stdClass());
        $filter(['foo' => 1]);
        array_filter($elements, $filter);
    }

    public function compose(ClosureExpressionVisitor $visitor, stdClass $object): void
    {
        $filter = $visitor->walkCompositeExpression(new CompositeExpression(
            CompositeExpression::TYPE_AND,
            [new Comparison('foo', Comparison::EQ, 1)],
        ));

        $filter($object);
        $filter(['foo' => 1]);
    }

    public function sort(stdClass $object, stdClass $other): void
    {
        $next   = ClosureExpressionVisitor::sortByField('foo');
        $sortBy = ClosureExpressionVisitor::sortByField('bar', 1, $next);

        $sortBy($object, $other);
        $sortBy(['bar' => 1], ['bar' => 2]);
    }

    public function match(Criteria $criteria): void
    {
        $collection = new ArrayCollection([['foo' => 1], ['foo' => 2]]);

        $collection->matching($criteria);
    }

    public function compositeChildren(ClosureExpressionVisitor $visitor, stdClass $object): void
    {
        $expression = new CompositeExpression(CompositeExpression::TYPE_AND, [
            new Comparison('foo', Comparison::EQ, 1),
            new CompositeExpression(CompositeExpression::TYPE_OR, [
                new Comparison('bar', Comparison::EQ, 2),
            ]),
        ]);

        foreach ($expression->getExpressionList() as $child) {
            $filter = $visitor->dispatch($child);

            $filter($object);
            $filter(['foo' => 1]);
        }
    }
}
