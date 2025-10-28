<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

class TestObject
{
    public function __construct(
        public mixed $foo = null,
        public mixed $bar = null,
        public mixed $baz = null,
        public mixed $qux = null,
    ) {
    }
}
