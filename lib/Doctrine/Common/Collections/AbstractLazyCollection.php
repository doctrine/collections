<?php


namespace Doctrine\Common\Collections;

use Doctrine\Collections\AbstractLazyCollection as NewClass;

class_alias(NewClass::class, AbstractLazyCollection::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    AbstractLazyCollection::class,
    NewClass::class
), E_USER_DEPRECATED);
