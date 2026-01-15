Note about upgrading: Doctrine uses static and runtime mechanisms to raise
awareness about deprecated code.

- Use of `@deprecated` docblock that is detected by IDEs (like PHPStorm) or
  Static Analysis tools (like Psalm, phpstan)
- Use of our low-overhead runtime deprecation API, details:
  https://github.com/doctrine/deprecations/

# Upgrade to 2.6

When extending `Doctrine\Common\Collections\AbstractLazyCollection`, the
backing collection initialized in `doInitialize()` must implement
`Doctrine\Common\Collections\Selectable`. Initializing with a collection that
does not implement `Selectable` is deprecated and will throw an exception in 3.0.

Also, implementing `ReadableCollection` without implementing `Selectable`
deprecated and will be an error in 3.0.

# Upgrade to 2.5

Extending the following classes is deprecated and will no longer be possible in 3.0:

- `Doctrine\Common\Collections\Criteria`
- `Doctrine\Common\Collections\Expr\ClosureExpressionVisitor`
- `Doctrine\Common\Collections\Expr\Comparison`
- `Doctrine\Common\Collections\Expr\CompositeExpression`
- `Doctrine\Common\Collections\Expr\Value`
- `Doctrine\Common\Collections\ExpressionBuilder`

# Upgrade to 2.4

## Deprecated accessing fields through other means than raw field access when using the criteria filtering API (the `Doctrine\Common\Collections\Selectable` interface)

Starting with the next major version, the only way to access data when using the criteria filtering 
API is through direct (reflection-based) access at properties directly, also bypassing property hooks.
This is to ensure consistency with how the ORM/ODM works. See https://github.com/doctrine/collections/pull/472 for
the full motivation.

To opt-in to the new behaviour, pass `true` for the `$accessRawFieldValues` parameter when creating a `Criteria`
object through either `Doctrine\Common\Collections\Criteria::create()` or when calling the `Doctrine\Common\Collections\Criteria` constructor.

Be aware that switching to reflection-based field access may prevent ORM or ODM proxy objects
becoming initialized, since their triggers (like calling public methods) are bypassed. This might lead
to `null` values being read from such objects, which may cause wrong filtering or sorting results.
To avoid this issue, use native lazy objects added in PHP 8.4.
See https://github.com/doctrine/collections/issues/487 for more details on when this may happen.

# Upgrade to 2.2

## Deprecated string representation of sort order

Criteria orderings direction is now represented by the
`Doctrine\Common\Collection\Order` enum.

As a consequence:

- `Criteria::ASC` and `Criteria::DESC` are deprecated in favor of
  `Order::Ascending` and `Order::Descending`, respectively.
- `Criteria::getOrderings()` is deprecated in favor of `Criteria::orderings()`,
  which returns `array<string, Order>`.
- `Criteria::orderBy()` accepts `array<string, string|Order>`, but passing
  anything other than `array<string, Order>` is deprecated.

# Upgrade to 2.0

## BC breaking changes

Native parameter types were added. Native return types will be added in 3.0.x
As a consequence, some signatures were changed and will have to be adjusted in sub-classes.

Note that in order to keep compatibility with both 1.x and 2.x versions,
extending code would have to omit the added parameter types.
This would only work in PHP 7.2+ which is the first version featuring
[parameter widening](https://wiki.php.net/rfc/parameter-no-type-variance).
It is also recommended to add return types according to the tables below

You can find a list of major changes to public API below.

### Doctrine\Common\Collections\Collection

|             1.0.x                |                  3.0.x                           |
|---------------------------------:|:-------------------------------------------------|
| `add($element)`                  | `add(mixed $element): void`                      |
| `clear()`                        | `clear(): void`                                  |
| `contains($element)`             | `contains(mixed $element): bool`                 |
| `isEmpty()`                      | `isEmpty(): bool`                                |
| `removeElement($element)`        | `removeElement(mixed $element): bool`            |
| `containsKey($key)`              | `containsKey(string\|int $key): bool`            |
| `get()`                          | `get(string\|int $key): mixed`                   |
| `getKeys()`                      | `getKeys(): array`                               |
| `getValues()`                    | `getValues(): array`                             |
| `set($key, $value)`              | `set(string\|int $key, $value): void`            |
| `toArray()`                      | `toArray(): array`                               |
| `first()`                        | `first(): mixed`                                 |
| `last()`                         | `last(): mixed`                                  |
| `key()`                          | `key(): int\|string\|null`                        |
| `current()`                      | `current(): mixed`                               |
| `next()`                         | `next(): mixed`                                  |
| `exists(Closure $p)`             | `exists(Closure $p): bool`                       |
| `filter(Closure $p)`             | `filter(Closure $p): self`                       |
| `forAll(Closure $p)`             | `forAll(Closure $p): bool`                       |
| `map(Closure $func)`             | `map(Closure $func): self`                       |
| `partition(Closure $p)`          | `partition(Closure $p): array`                   |
| `indexOf($element)`              | `indexOf(mixed $element): int\|string\|false`    |
| `slice($offset, $length = null)` | `slice(int $offset, ?int $length = null): array` |
| `count()`                        | `count(): int`                                   |
| `getIterator()`                  | `getIterator(): \Traversable`                    |
| `offsetSet($offset, $value)`     | `offsetSet(mixed $offset, mixed $value): void`   |
| `offsetUnset($offset)`           | `offsetUnset(mixed $offset): void`               |
| `offsetExists($offset)`          | `offsetExists(mixed $offset): bool`              |

### Doctrine\Common\Collections\AbstractLazyCollection

|      1.0.x        |         3.0.x           |
|------------------:|:------------------------|
| `isInitialized()` | `isInitialized(): bool` |
| `initialize()`    | `initialize(): void`    |
| `doInitialize()`  | `doInitialize(): void`  |

### Doctrine\Common\Collections\ArrayCollection

|            1.0.x              |               3.0.x                   |
|------------------------------:|:--------------------------------------|
| `createFrom(array $elements)` | `createFrom(array $elements): static` |
| `__toString()`                | `__toString(): string`                |

### Doctrine\Common\Collections\Criteria

|            1.0.x                          |               3.0.x                         |
|------------------------------------------:|:--------------------------------------------|
| `where(Expression $expression): self`     | `where(Expression $expression): static`     |
| `andWhere(Expression $expression): self`  | `andWhere(Expression $expression): static`  |
| `orWhere(Expression $expression): self`   | `orWhere(Expression $expression): static`   |
| `orderBy(array $orderings): self`         | `orderBy(array $orderings): static`         |
| `setFirstResult(?int $firstResult): self` | `setFirstResult(?int $firstResult): static` |
| `setMaxResult(?int $maxResults): self`    | `setMaxResults(?int $maxResults): static`   |

### Doctrine\Common\Collections\Selectable

|             1.0.x              |                   3.0.x                    |
|-------------------------------:|:-------------------------------------------|
| `matching(Criteria $criteria)` | `matching(Criteria $criteria): Collection` |

# Upgrade to 1.7

## Deprecated null first result

Passing null as `$firstResult` to
`Doctrine\Common\Collections\Criteria::__construct()` and to
`Doctrine\Common\Collections\Criteria::setFirstResult()` is deprecated.
Use `0` instead.
