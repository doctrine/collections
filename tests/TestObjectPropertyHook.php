<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

class TestObjectPropertyHook
{
    public int|null $fooBar = null {
        get {
            return -$this->fooBar; // a different value
        }
    }
}
