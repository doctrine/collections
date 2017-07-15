<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

declare(strict_types=1);

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Value;

/**
 * Builder for Expressions in the {@link Selectable} interface.
 *
 * Important Notice for interoperable code: You have to use scalar
 * values only for comparisons, otherwise the behavior of the comparison
 * may be different between implementations (Array vs ORM vs ODM).
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since  2.3
 */
class ExpressionBuilder
{
    /**
     * @param mixed $x
     *
     * @return CompositeExpression
     */
    public function andX($x = null): CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
    }

    /**
     * @param mixed $x
     *
     * @return CompositeExpression
     */
    public function orX($x = null): CompositeExpression
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function eq(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::EQ, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function gt(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::GT, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function lt($field, $value)
    {
        return new Comparison($field, Comparison::LT, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function gte(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::GTE, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function lte(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::LTE, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function neq(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::NEQ, new Value($value));
    }

    /**
     * @param string $field
     *
     * @return Comparison
     */
    public function isNull(string $field): Comparison
    {
        return new Comparison($field, Comparison::EQ, new Value(null));
    }

    /**
     * @param string $field
     * @param mixed  $values
     *
     * @return Comparison
     */
    public function in(string $field, array $values): Comparison
    {
        return new Comparison($field, Comparison::IN, new Value($values));
    }

    /**
     * @param string $field
     * @param mixed  $values
     *
     * @return Comparison
     */
    public function notIn(string $field, array $values): Comparison
    {
        return new Comparison($field, Comparison::NIN, new Value($values));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function contains(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::CONTAINS, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function memberOf(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::MEMBER_OF, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function startsWith(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::STARTS_WITH, new Value($value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison
     */
    public function endsWith(string $field, $value): Comparison
    {
        return new Comparison($field, Comparison::ENDS_WITH, new Value($value));
    }    

}
