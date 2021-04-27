<?php

namespace Doctrine\Common\Collections;

use Doctrine\Collections\ArrayCollection as NewClass;

class_alias(NewClass::class, ArrayCollection::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    ArrayCollection::class,
    NewClass::class
), E_USER_DEPRECATED);
