<?php

namespace Doctrine\Common\Collections;

use Doctrine\Collections\Selectable as NewInterface;

class_alias(NewInterface::class, Selectable::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    Selectable::class,
    NewClass::class
), E_USER_DEPRECATED);
