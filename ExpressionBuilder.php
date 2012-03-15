<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYvalue HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYvalue
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Value;

/**
 * Builder for Expressions in the {@link Selectable} interface.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since 2.3
 */
class ExpressionBuilder
{
    public function andX($x = null)
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
    }

    public function orX($x = null)
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
    }

    public function eq($field, $value)
    {
        return new Comparison($field, Comparison::EQ, new Value($value));
    }

    public function gt($field, $value)
    {
        return new Comparison($field, Comparison::GT, new Value($value));
    }

    public function lt($field, $value)
    {
        return new Comparison($field, Comparison::LT, new Value($value));
    }

    public function gte($field, $value)
    {
        return new Comparison($field, Comparison::GTE, new Value($value));
    }

    public function lte($field, $value)
    {
        return new Comparison($field, Comparison::LTE, new Value($value));
    }

    public function neq($field, $value)
    {
        return new Comparison($field, Comparison::NEQ, new Value($value));
    }

    public function isNull($field)
    {
        return new Comparison($field, Comparison::IS, new Value(null));
    }

    public function in($field, array $values)
    {
        return new Comparison($field, Comparison::IN, new Value($values));
    }

    public function notIn($field, array $values)
    {
        return new Comparison($field, Comparison::NIN, new Value($values));
    }
}

