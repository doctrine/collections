<?php

namespace Doctrine\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use stdClass;

/**
 * Simple collection implements different constructor semantics.
 */
final class DerivedArrayCollection extends ArrayCollection
{
    /** @var stdClass */
    private $foo;

    /** @param mixed[] $elements */
    public function __construct(stdClass $foo, array $elements = [])
    {
        $this->foo = $foo;

        parent::__construct($elements);
    }

    /**
     * @param mixed[] $elements
     *
     * @return self<mixed>
     */
    protected function createFrom(array $elements): self
    {
        return new static($this->foo, $elements);
    }
}
