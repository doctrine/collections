Serialization
=============

Using (un-)serialize() on a collection is not a supported use-case
and may break when changes on the collection's internals happen in the future.
If a collection needs to be serialized, use ``toArray()`` and reconstruct
the collection manually.

.. code-block:: php

    $collection = new ArrayCollection([1, 2, 3]);
    $serialized = serialize($collection->toArray());

A reconstruction is also necessary when the collection contains objects with
infinite recursion of dependencies like in this ``json_serialize()`` example:

.. code-block:: php

    $foo = new Foo();
    $bar = new Bar();

    $foo->setBar($bar);
    $bar->setFoo($foo);

    $collection = new ArrayCollection([$foo]);
    $json       = json_serialize($collection->toArray()); // recursion detected

Serializer libraries can be used to create the serialization-output to prevent
errors.
