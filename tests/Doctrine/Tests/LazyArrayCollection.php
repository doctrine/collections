<?php

namespace Doctrine\Tests;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Simple lazy collection that used an ArrayCollection as backed collection
 */
class LazyArrayCollection extends AbstractLazyCollection
{
    private $_onInitialization;

    public function __construct(Collection $collection)
    {
        $this->_onInitialization = $collection;
    }

    /**
     * Do the initialization logic
     *
     * @return void
     */
    protected function doInitialize()
    {
        $this->collection = $this->_onInitialization;
    }
}
