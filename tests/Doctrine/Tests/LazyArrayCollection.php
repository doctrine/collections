<?php

declare(strict_types=1);

namespace Doctrine\Tests;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Simple lazy collection that used an ArrayCollection as backed collection.
 */
class LazyArrayCollection extends AbstractLazyCollection
{
    /** @param Collection<mixed> $collectionOnInitialization Apply the collection only in method doInitialize */
    public function __construct(
        private readonly Collection $collectionOnInitialization,
    ) {
    }

    /**
     * Do the initialization logic.
     */
    protected function doInitialize(): void
    {
        $this->collection = $this->collectionOnInitialization;
    }
}
