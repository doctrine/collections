# Doctrine Collections

[![Build Status](https://travis-ci.org/doctrine/collections.svg?branch=master)](https://travis-ci.org/doctrine/collections)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/doctrine/collections/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/doctrine/collections/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/doctrine/collections/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/doctrine/collections/?branch=master)

Collections Abstraction library

## Changelog

### v1.5.0

* [Require PHP 7.1+](https://github.com/doctrine/collections/pull/105)
* [Drop HHVM support](https://github.com/doctrine/collections/pull/118)

### v1.4.0

* [Require PHP 5.6+](https://github.com/doctrine/collections/pull/105)
* [Add `ArrayCollection::createFrom()`](https://github.com/doctrine/collections/pull/91)
* [Support non-camel-case naming](https://github.com/doctrine/collections/pull/57)
* [Comparison `START_WITH`, `END_WITH`](https://github.com/doctrine/collections/pull/78)
* [Comparison `MEMBER_OF`](https://github.com/doctrine/collections/pull/66)
* [Add Contributing guide](https://github.com/doctrine/collections/pull/103)

### v1.3.0

* [Explicit casting of first and max results in criteria API](https://github.com/doctrine/collections/pull/26)
* [Keep keys when using `ArrayCollection#matching()` with sorting](https://github.com/doctrine/collections/pull/49)
* [Made `AbstractLazyCollection#$initialized` protected for extensibility](https://github.com/doctrine/collections/pull/52)

### v1.2.0

* Add a new ``AbstractLazyCollection``

### v1.1.0

* Deprecated ``Comparison::IS``, because it's only there for SQL semantics.
  These are fixed in the ORM instead.
* Add ``Comparison::CONTAINS`` to perform partial string matches:

        $criteria->andWhere($criteria->expr()->contains('property', 'Foo'));
