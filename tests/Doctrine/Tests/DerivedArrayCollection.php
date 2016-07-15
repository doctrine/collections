<?php
namespace Doctrine\Tests;

use Doctrine\Common\Collections\ArrayCollection;

final class DerivedArrayCollection extends ArrayCollection
{
    private $foo;

    public function __construct(\stdClass $foo, array $elements = array())
    {
        $this->foo = $foo;

        parent::__construct($elements);
    }

    protected function createFrom(array $elements)
    {
        return new static($this->foo, $elements);
    }
}
