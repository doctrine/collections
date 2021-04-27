<?php

namespace Doctrine\Common\Collections;

use Doctrine\Collections\ExpressionBuilder as NewClass;

class_alias(NewClass::class, ExpressionBuilder::class);

@trigger_error(sprintf(
    'The "%s" class is deprecated use "%s" instead.',
    ExpressionBuilder::class,
    NewClass::class
), E_USER_DEPRECATED);
