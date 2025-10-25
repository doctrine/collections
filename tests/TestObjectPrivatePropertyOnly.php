<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

class TestObjectPrivatePropertyOnly
{
    public function __construct(private mixed $fooBar = null)
    {
    }
}
