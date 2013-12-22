# Doctrine Collections

Collections Abstraction library

## Changelog

### v1.2

* Add a new ``AbstractLazyCollection``

### v1.1

* Deprecated ``Comparison::IS``, because it's only there for SQL semantics.
  These are fixed in the ORM instead.
* Add ``Comparison::CONTAINS`` to perform partial string matches:

        $criteria->andWhere($criteria->expr()->contains('property', 'Foo'));
