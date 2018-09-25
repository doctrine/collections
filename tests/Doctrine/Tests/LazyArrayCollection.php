<?php

namespace Doctrine\Tests;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Simple lazy collection that used an ArrayCollection as backed collection.
 */
class LazyArrayCollection extends AbstractLazyCollection
{
    /**
     * Apply the collection only in method doInitialize
     *
     * @var Collection
     */
    private $collectionOnInitialization;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collectionOnInitialization = $collection;
    }

    /**
     * Do the initialization logic.
     */
    protected function doInitialize() : void
    {
        $this->collection = $this->collectionOnInitialization;
    }
}
