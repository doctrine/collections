<?php

namespace Doctrine\Common\Collections\Expr;

use Doctrine\Collections\Expr\ExpressionVisitor as NewClass;

class_alias(NewClass::class, ExpressionVisitor::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    ExpressionVisitor::class,
    NewClass::class
), E_USER_DEPRECATED);
