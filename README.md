# Doctrine Collections

Collections Abstraction library

## Changelog

### v1.1

* Add ``Comparison::CONTAINS`` to perform partial string matches:

        $criteria->andWhere($criteria->expr()->contains('property', 'Foo'));
