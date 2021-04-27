<?php

namespace Doctrine\Common\Collections\Expr;

use Doctrine\Collections\Expr\Comparison as NewClass;

class_alias(NewClass::class, Comparison::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    Comparison::class,
    NewClass::class
), E_USER_DEPRECATED);
