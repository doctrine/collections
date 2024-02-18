<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use stdClass;

/**
 * Simple collection implements different constructor semantics.
 */
final class DerivedArrayCollection extends ArrayCollection
{
    /** @param mixed[] $elements */
    public function __construct(private readonly stdClass $foo, array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * @param mixed[] $elements
     *
     * @return self<mixed>
     */
    protected function createFrom(array $elements): static
    {
        return new static($this->foo, $elements);
    }
}
