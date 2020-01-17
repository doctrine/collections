Upgrading from 1.x to 2.0
=========================

## BC breaking changes

Native parameter and return types were added.
As a consequence, some signatures were changed and will have to be adjusted in sub-classes.

Note that in order to keep compatibility with both 1.x and 2.x versions, extending code would have to omit the added parameter types and add the return types. This would only work in PHP 7.2+ which is the first version featuring [parameter widening](https://wiki.php.net/rfc/parameter-no-type-variance).

You can find a list of major changes to public API below.

#### Doctrine\Common\Collections\Collection

|             before             |                  after                         |
|-------------------------------:|:-----------------------------------------------|
| add($element)                  | add($element): bool                            |
| clear()                        | clear(): void                                  |
| contains($element)             | contains($element): bool                       |
| isEmpty()                      | isEmpty(): bool                                |
| removeElement($element)        | removeElement($element): bool                  |
| containsKey($key)              | containsKey($key): bool                        |
| getKeys()                      | getKeys(): array                               |
| getValues()                    | getValues(): array                             |
| set($key, $value)              | set($key, $value): void                        |
| toArray()                      | toArray(): array                               |
| exists(Closure $p)             | exists(Closure $p): bool                       |
| filter(Closure $p)             | filter(Closure $p): self                       |
| forAll(Closure $p)             | forAll(Closure $p): bool                       |
| map(Closure $func)             | map(Closure $func): self                       |
| partition(Closure $p)          | partition(Closure $p): array                   |
| slice($offset, $length = null) | slice(int $offset, ?int $length = null): array |
| count()                        | count(): int                                   |
| getIterator()                  | getIterator(): \Traversable                    |
| offsetSet($offset, $value)     | offsetSet($offset, $value): void               |
| offsetUnset($offset)           | offsetUnset($offset): void                     |
| offsetExists($offset)          | offsetExists($offset): bool                    |

#### Doctrine\Common\Collections\AbstractLazyCollection

|      before     |         after         |
|----------------:|:----------------------|
| isInitialized() | isInitialized(): bool |
| initialize()    | initialize(): void    |
| doInitialize()  | doInitialize(): void  |

#### Doctrine\Common\Collections\ArrayCollection

|            before           |               after               |
|----------------------------:|:----------------------------------|
| createFrom(array $elements) | createFrom(array $elements): self |
| __toString()                | __toString(): string              |

#### Doctrine\Common\Collections\Selectable

|             before           |                   after                  |
|-----------------------------:|:-----------------------------------------|
| matching(Criteria $criteria) | matching(Criteria $criteria): Collection |
