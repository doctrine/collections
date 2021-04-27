<?php

namespace Doctrine\Common\Collections\Expr;

use Doctrine\Collections\Expr\CompositeExpression as NewClass;

class_alias(NewClass::class, CompositeExpression::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    CompositeExpression::class,
    NewClass::class
), E_USER_DEPRECATED);
