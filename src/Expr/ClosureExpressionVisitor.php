<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections\Expr;

use Closure;
use Doctrine\Deprecations\Deprecation;
use Override;
use ReflectionClass;
use RuntimeException;

use function array_all;
use function array_any;
use function explode;
use function func_num_args;
use function in_array;
use function is_array;
use function is_scalar;
use function iterator_to_array;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_starts_with;

/**
 * Walks an expression graph and turns it into a PHP closure.
 *
 * This closure can be used with {@Collection#filter()} and is used internally
 * by {@ArrayCollection#select()}.
 */
final class ClosureExpressionVisitor extends ExpressionVisitor
{
    public function __construct(
        // @phpstan-ignore property.onlyWritten (that property is deprecated, kept for BC)
        private readonly bool $accessRawFieldValues = false,
    ) {
    }

    /**
     * Accesses the raw field value of a given object.
     *
     * @param object|mixed[] $object
     */
    public static function getObjectFieldValue(object|array $object, string $field): mixed
    {
        if (func_num_args() === 3) {
            Deprecation::trigger(
                'doctrine/collections',
                'https://github.com/doctrine/collections/pull/486',
                'The `accessRawFieldValues` parameter passed to %s is deprecated and a no-op. You can remove it.',
                __METHOD__,
            );
        }

        if (str_contains($field, '.')) {
            [$field, $subField] = explode('.', $field, 2);
            $object             = self::getObjectFieldValue($object, $field);

            return self::getObjectFieldValue($object, $subField);
        }

        if (is_array($object)) {
            return $object[$field];
        }

        $reflectionClass = new ReflectionClass($object);

        while ($reflectionClass && ! $reflectionClass->hasProperty($field)) {
            $reflectionClass = $reflectionClass->getParentClass();
        }

        if ($reflectionClass === false) {
            throw new RuntimeException(sprintf('Field "%s" does not exist in class "%s"', $field, $object::class));
        }

        $property = $reflectionClass->getProperty($field);

        return $property->getRawValue($object);
    }

    /**
     * Helper for sorting arrays of objects based on multiple fields + orientations.
     *
     * @return Closure(mixed, mixed): int
     */
    public static function sortByField(string $name, int $orientation = 1, Closure|null $next = null): Closure
    {
        if (func_num_args() === 4) {
            Deprecation::trigger(
                'doctrine/collections',
                'https://github.com/doctrine/collections/pull/486',
                'The `accessRawFieldValues` parameter passed to %s is deprecated and a no-op. You can remove it.',
                __METHOD__,
            );
        }

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

    #[Override]
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

    #[Override]
    public function walkValue(Value $value): mixed
    {
        return $value->getValue();
    }

    #[Override]
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

    /**
     * @param array<callable(object): bool> $expressions
     *
     * @return Closure(object): bool
     */
    private function andExpressions(array $expressions): Closure
    {
        return static fn (object $object): bool => array_all(
            $expressions,
            static fn (callable $expression): bool => (bool) $expression($object),
        );
    }

    /**
     * @param array<callable(object): bool> $expressions
     *
     * @return Closure(object): bool
     */
    private function orExpressions(array $expressions): Closure
    {
        return static fn (object $object): bool => array_any(
            $expressions,
            static fn (callable $expression): bool => (bool) $expression($object),
        );
    }

    /**
     * @param array<callable(object): bool> $expressions
     *
     * @return Closure(object): bool
     */
    private function notExpression(array $expressions): Closure
    {
        return static fn (object $object) => ! $expressions[0]($object);
    }
}
