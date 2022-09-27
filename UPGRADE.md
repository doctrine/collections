Note about upgrading: Doctrine uses static and runtime mechanisms to raise
awareness about deprecated code.

- Use of `@deprecated` docblock that is detected by IDEs (like PHPStorm) or
  Static Analysis tools (like Psalm, phpstan)
- Use of our low-overhead runtime deprecation API, details:
  https://github.com/doctrine/deprecations/

# Upgrade to 2.0

## BC breaking changes

Native parameter and return types were added.
As a consequence, some signatures were changed and will have to be adjusted in sub-classes.

Note that in order to keep compatibility with both 1.x and 2.x versions,
extending code would have to omit the added parameter types and add the return
types. This would only work in PHP 7.2+ which is the first version featuring
[parameter widening](https://wiki.php.net/rfc/parameter-no-type-variance).

You can find a list of major changes to public API below.

### Doctrine\Common\Collections\Collection

|             before             |                  after                  |
|-------------------------------:|:----------------------------------------|
| add($element)                  | add(mixed $element)                     |
| contains($element)             | contains(mixed $element)                |
| removeElement($element)        | removeElement(mixed $element)           |
| containsKey($key)              | containsKey(string|int $key)            |
| get()                          | get(string|int $key)                    |
| set($key, $value)              | set(string|int $key, $value)            |
| indexOf($element)              | indexOf(mixed $element)                 |
| slice($offset, $length = null) | slice(int $offset, ?int $length = null) |
| offsetSet($offset, $value)     | offsetSet(mixed $offset, mixed $value)  |
| offsetUnset($offset)           | offsetUnset(mixed $offset)              |
| offsetExists($offset)          | offsetExists(mixed $offset)             |


### Doctrine\Common\Collections\Criteria

|            before                       |               after                       |
|----------------------------------------:|:------------------------------------------|
| where(Expression $expression): self     | where(Expression $expression): static     |
| andWhere(Expression $expression): self  | andWhere(Expression $expression): static  |
| orWhere(Expression $expression): self   | orWhere(Expression $expression): static   |
| orderBy(array $orderings): self         | orderBy(array $orderings): static         |
| setFirstResult(?int $firstResult): self | setFirstResult(?int $firstResult): static |
| setMaxResult(?int $maxResults): self    | setMaxResults(?int $maxResults): static   |
