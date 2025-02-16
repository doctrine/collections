<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

enum Order: string
{
    case Ascending  = 'ASC';
    case Descending = 'DESC';
}
