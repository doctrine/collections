<?php

declare(strict_types=1);

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

    public function __construct(stdClass $foo, array $elements = [])
    {
        $this->foo = $foo;

        parent::__construct($elements);
    }

    protected function createFrom(array $elements) : parent
    {
        return new static($this->foo, $elements);
    }
}
