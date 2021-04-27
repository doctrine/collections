<?php

namespace Doctrine\Common\Collections;

use Doctrine\Collections\Criteria as NewClass;

class_alias(NewClass::class, Criteria::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    Criteria::class,
    NewClass::class
), E_USER_DEPRECATED);
