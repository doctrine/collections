<?php

namespace Doctrine\Common\Collections\Expr;

use function in_array;
use function is_array;
use function iterator_to_array;
use function method_exists;
use function preg_replace_callback;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;

/**
 * Walks an expression graph and turns it into a PHP closure.
 *
 * This closure can be used with {@Collection#filter()} and is used internally
 * by {@ArrayCollection#select()}.
 */
class ClosureExpressionVisitor extends ExpressionVisitor
{
    /**
     * Accesses the field of a given object. This field has to be public
     * directly or indirectly (through an accessor get*, is*, or a magic
     * method, __get, __call).
     *
     * @param object|array $object
     * @param string       $field
     *
     * @return mixed
     */
    public static function getObjectFieldValue($object, $field)
    {
        if (is_array($object)) {
            return $object[$field];
        }

        $accessors = ['get', 'is'];

        foreach ($accessors as $accessor) {
            $accessor .= $field;

            if (! method_exists($object, $accessor)) {
                continue;
            }

            return $object->$accessor();
        }

        // __call should be triggered for get.
        $accessor = $accessors[0] . $field;

        if (method_exists($object, '__call')) {
            return $object->$accessor();
        }

        if ($object instanceof \ArrayAccess) {
            return $object[$field];
        }

        if (isset($object->$field)) {
            return $object->$field;
        }

        // camelcase field name to support different variable naming conventions
        $ccField = preg_replace_callback('/_(.?)/', function ($matches) {
            return strtoupper($matches[1]);
        }, $field);

        foreach ($accessors as $accessor) {
            $accessor .= $ccField;

            if (! method_exists($object, $accessor)) {
                continue;
            }

            return $object->$accessor();
        }

        return $object->$field;
    }

    /**
     * Helper for sorting arrays of objects based on multiple fields + orientations.
     *
     * @param string $name
     * @param int    $orientation
     *
     * @return \Closure
     */
    public static function sortByField($name, $orientation = 1, ?\Closure $next = null)
    {
        if (! $next) {
            $next = function () : int {
                return 0;
            };
        }

        return function ($a, $b) use ($name, $next, $orientation) : int {
            $aValue = ClosureExpressionVisitor::getObjectFieldValue($a, $name);
            $bValue = ClosureExpressionVisitor::getObjectFieldValue($b, $name);

            if ($aValue === $bValue) {
                return $next($a, $b);
            }

            return (($aValue > $bValue) ? 1 : -1) * $orientation;
        };
    }

    /**
     * {@inheritDoc}
     */
    public function walkComparison(Comparison $comparison)
    {
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue(); // shortcut for walkValue()

        switch ($comparison->getOperator()) {
            case Comparison::EQ:
                return function ($object) use ($field, $value) : bool {
                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) === $value;
                };

            case Comparison::NEQ:
                return function ($object) use ($field, $value) : bool {
                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) !== $value;
                };

            case Comparison::LT:
                return function ($object) use ($field, $value) : bool {
                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) < $value;
                };

            case Comparison::LTE:
                return function ($object) use ($field, $value) : bool {
                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) <= $value;
                };

            case Comparison::GT:
                return function ($object) use ($field, $value) : bool {
                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) > $value;
                };

            case Comparison::GTE:
                return function ($object) use ($field, $value) : bool {
                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) >= $value;
                };

            case Comparison::IN:
                return function ($object) use ($field, $value) : bool {
                    return in_array(ClosureExpressionVisitor::getObjectFieldValue($object, $field), $value, true);
                };

            case Comparison::NIN:
                return function ($object) use ($field, $value) : bool {
                    return ! in_array(ClosureExpressionVisitor::getObjectFieldValue($object, $field), $value, true);
                };

            case Comparison::CONTAINS:
                return function ($object) use ($field, $value) {
                    return strpos(ClosureExpressionVisitor::getObjectFieldValue($object, $field), $value) !== false;
                };

            case Comparison::MEMBER_OF:
                return function ($object) use ($field, $value) : bool {
                    $fieldValues = ClosureExpressionVisitor::getObjectFieldValue($object, $field);
                    if (! is_array($fieldValues)) {
                        $fieldValues = iterator_to_array($fieldValues);
                    }
                    return in_array($value, $fieldValues, true);
                };

            case Comparison::STARTS_WITH:
                return function ($object) use ($field, $value) : bool {
                    return strpos(ClosureExpressionVisitor::getObjectFieldValue($object, $field), $value) === 0;
                };

            case Comparison::ENDS_WITH:
                return function ($object) use ($field, $value) : bool {
                    return $value === substr(ClosureExpressionVisitor::getObjectFieldValue($object, $field), -strlen($value));
                };

            default:
                throw new \RuntimeException('Unknown comparison operator: ' . $comparison->getOperator());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function walkCompositeExpression(CompositeExpression $expr)
    {
        $expressionList = [];

        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        switch ($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                return $this->andExpressions($expressionList);

            case CompositeExpression::TYPE_OR:
                return $this->orExpressions($expressionList);

            default:
                throw new \RuntimeException('Unknown composite ' . $expr->getType());
        }
    }

    /**
     * @param array $expressions
     */
    private function andExpressions(array $expressions) : callable
    {
        return function ($object) use ($expressions) : bool {
            foreach ($expressions as $expression) {
                if (! $expression($object)) {
                    return false;
                }
            }

            return true;
        };
    }

    /**
     * @param array $expressions
     */
    private function orExpressions(array $expressions) : callable
    {
        return function ($object) use ($expressions) : bool {
            foreach ($expressions as $expression) {
                if ($expression($object)) {
                    return true;
                }
            }

            return false;
        };
    }
}
