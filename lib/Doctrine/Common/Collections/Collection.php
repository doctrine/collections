<?php

namespace Doctrine\Common\Collections;

use Doctrine\Collections\Collection as NewClass;

class_alias(NewClass::class, Collection::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    Collection::class,
    NewClass::class
), E_USER_DEPRECATED);
