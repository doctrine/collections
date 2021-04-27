<?php

namespace Doctrine\Common\Collections\Expr;

use Doctrine\Collections\Expr\Value as NewClass;

class_alias(NewClass::class, Value::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    Value::class,
    NewClass::class
), E_USER_DEPRECATED);
