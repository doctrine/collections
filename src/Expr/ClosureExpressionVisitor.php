<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use ArrayAccess;
use Closure;
use RuntimeException;

use function array_all;
use function array_any;
use function explode;
use function in_array;
use function is_array;
use function is_scalar;
use function iterator_to_array;
use function method_exists;
use function preg_match;
use function preg_replace_callback;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function strtoupper;

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
     * @param object|mixed[] $object
     */
    public static function getObjectFieldValue(object|array $object, string $field): mixed
    {
        if (str_contains($field, '.')) {
            [$field, $subField] = explode('.', $field, 2);
            $object             = self::getObjectFieldValue($object, $field);

            return self::getObjectFieldValue($object, $subField);
        }

        if (is_array($object)) {
            return $object[$field];
        }

        $accessors = ['get', 'is', ''];

        foreach ($accessors as $accessor) {
            $accessor .= $field;

            if (method_exists($object, $accessor)) {
                return $object->$accessor();
            }
        }

        if (preg_match('/^is[A-Z]+/', $field) === 1 && method_exists($object, $field)) {
            return $object->$field();
        }

        // __call should be triggered for get.
        $accessor = $accessors[0] . $field;

        if (method_exists($object, '__call')) {
            return $object->$accessor();
        }

        if ($object instanceof ArrayAccess) {
            return $object[$field];
        }

        if (isset($object->$field)) {
            return $object->$field;
        }

        // camelcase field name to support different variable naming conventions
        $ccField = preg_replace_callback('/_(.?)/', static fn (array $matches) => strtoupper((string) $matches[1]), $field);

        foreach ($accessors as $accessor) {
            $accessor .= $ccField;

            if (method_exists($object, $accessor)) {
                return $object->$accessor();
            }
        }

        return $object->$field;
    }

    /**
     * Helper for sorting arrays of objects based on multiple fields + orientations.
     */
    public static function sortByField(string $name, int $orientation = 1, Closure|null $next = null): Closure
    {
        if (! $next) {
            $next = static fn (): int => 0;
        }

        return static function (mixed $a, mixed $b) use ($name, $next, $orientation): int {
            $aValue = ClosureExpressionVisitor::getObjectFieldValue($a, $name);

            $bValue = ClosureExpressionVisitor::getObjectFieldValue($b, $name);

            if ($aValue === $bValue) {
                return $next($a, $b);
            }

            return ($aValue > $bValue ? 1 : -1) * $orientation;
        };
    }

    public function walkComparison(Comparison $comparison): Closure
    {
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue();

        return match ($comparison->getOperator()) {
            Comparison::EQ => static fn (object|array $object): bool => self::getObjectFieldValue($object, $field) === $value,
            Comparison::NEQ => static fn (object|array $object): bool => self::getObjectFieldValue($object, $field) !== $value,
            Comparison::LT => static fn (object|array $object): bool => self::getObjectFieldValue($object, $field) < $value,
            Comparison::LTE => static fn (object|array $object): bool => self::getObjectFieldValue($object, $field) <= $value,
            Comparison::GT => static fn (object|array $object): bool => self::getObjectFieldValue($object, $field) > $value,
            Comparison::GTE => static fn (object|array $object): bool => self::getObjectFieldValue($object, $field) >= $value,
            Comparison::IN => static function (object|array $object) use ($field, $value): bool {
                $fieldValue = ClosureExpressionVisitor::getObjectFieldValue($object, $field);

                return in_array($fieldValue, $value, is_scalar($fieldValue));
            },
            Comparison::NIN => static function (object|array $object) use ($field, $value): bool {
                $fieldValue = ClosureExpressionVisitor::getObjectFieldValue($object, $field);

                return ! in_array($fieldValue, $value, is_scalar($fieldValue));
            },
            Comparison::CONTAINS => static fn (object|array $object): bool => str_contains((string) self::getObjectFieldValue($object, $field), (string) $value),
            Comparison::MEMBER_OF => static function (object|array $object) use ($field, $value): bool {
                $fieldValues = ClosureExpressionVisitor::getObjectFieldValue($object, $field);

                if (! is_array($fieldValues)) {
                    $fieldValues = iterator_to_array($fieldValues);
                }

                return in_array($value, $fieldValues, true);
            },
            Comparison::STARTS_WITH => static fn (object|array $object): bool => str_starts_with((string) self::getObjectFieldValue($object, $field), (string) $value),
            Comparison::ENDS_WITH => static fn (object|array $object): bool => str_ends_with((string) self::getObjectFieldValue($object, $field), (string) $value),
            default => throw new RuntimeException('Unknown comparison operator: ' . $comparison->getOperator()),
        };
    }

    public function walkValue(Value $value): mixed
    {
        return $value->getValue();
    }

    public function walkCompositeExpression(CompositeExpression $expr): Closure
    {
        $expressionList = [];

        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        return match ($expr->getType()) {
            CompositeExpression::TYPE_AND => $this->andExpressions($expressionList),
            CompositeExpression::TYPE_OR => $this->orExpressions($expressionList),
            CompositeExpression::TYPE_NOT => $this->notExpression($expressionList),
            default => throw new RuntimeException('Unknown composite ' . $expr->getType()),
        };
    }

    /** @param callable[] $expressions */
    private function andExpressions(array $expressions): Closure
    {
        return static fn (object $object): bool => array_all(
            $expressions,
            static fn (callable $expression): bool => (bool) $expression($object),
        );
    }

    /** @param callable[] $expressions */
    private function orExpressions(array $expressions): Closure
    {
        return static fn (object $object): bool => array_any(
            $expressions,
            static fn (callable $expression): bool => (bool) $expression($object),
        );
    }

    /** @param callable[] $expressions */
    private function notExpression(array $expressions): Closure
    {
        return static fn (object $object) => ! $expressions[0]($object);
    }
}
