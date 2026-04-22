<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

/** @deprecated use \SortDirection instead */
enum Order: string
{
    case Ascending  = 'ASC';
    case Descending = 'DESC';
}
