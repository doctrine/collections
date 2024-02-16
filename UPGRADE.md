Note about upgrading: Doctrine uses static and runtime mechanisms to raise
awareness about deprecated code.

- Use of `@deprecated` docblock that is detected by IDEs (like PHPStorm) or
  Static Analysis tools (like Psalm, phpstan)
- Use of our low-overhead runtime deprecation API, details:
  https://github.com/doctrine/deprecations/

# Upgrade to 2.2

## Deprecated not using Criteria::ASC or Criteria::DESC for ordering

When calling `Criteria::orderBy()`, `Criteria::ASC` or `Criteria::DESC` should
be preferred.

```diff
-$criteria = Criteria::create()->orderBy(['foo' => 'asc']);
+$criteria = Criteria::create()->orderBy(['foo' => Criteria::ASC]);
```

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
