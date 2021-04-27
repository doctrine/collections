<?php

namespace Doctrine\Common\Collections\Expr;

use Doctrine\Collections\Expr\ClosureExpressionVisitor as NewClass;

class_alias(NewClass::class, ClosureExpressionVisitor::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    ClosureExpressionVisitor::class,
    NewClass::class
), E_USER_DEPRECATED);
