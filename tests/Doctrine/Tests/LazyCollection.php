<?php

namespace Doctrine\Tests;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Simple lazy collection that used an ArrayCollection as backed collection
 */
class LazyCollection extends AbstractLazyCollection
{
    /**
     * Do the initialization logic
     *
     * @return void
     */
    public function doInitialize()
    {
        $this->collection = new ArrayCollection(array('a', 'b', 'c'));
    }
}
