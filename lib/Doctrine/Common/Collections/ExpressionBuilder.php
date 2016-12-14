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

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Composition;

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
     * @return Composition\AndComposition
     * @throws \RuntimeException
     */
    public function andX($x = null)
    {
        return new Composition\AndComposition(func_get_args());
    }

    /**
     * @param mixed $x
     *
     * @return Composition\OrComposition
     * @throws \RuntimeException
     */
    public function orX($x = null)
    {
        return new Composition\OrComposition(func_get_args());
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\Equal
     */
    public function eq($field, $value)
    {
        return new Comparison\Equal($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\GreaterThan
     */
    public function gt($field, $value)
    {
        return new Comparison\GreaterThan($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\LessThan
     */
    public function lt($field, $value)
    {
        return new Comparison\LessThan($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\GreaterThanEqual
     */
    public function gte($field, $value)
    {
        return new Comparison\GreaterThanEqual($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\LessThanEqual
     */
    public function lte($field, $value)
    {
        return new Comparison\LessThanEqual($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\NotEqual
     */
    public function neq($field, $value)
    {
        return new Comparison\NotEqual($field, $value);
    }

    /**
     * @param string $field
     *
     * @return Comparison\Equal
     */
    public function isNull($field)
    {
        return new Comparison\Equal($field, null);
    }

    /**
     * @param string $field
     * @param mixed  $values
     *
     * @return Comparison\In
     */
    public function in($field, array $values)
    {
        return new Comparison\In($field, $values);
    }

    /**
     * @param string $field
     * @param array  $values
     *
     * @return Comparison\NotIn
     */
    public function notIn($field, array $values)
    {
        return new Comparison\NotIn($field, $values);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\Contains
     */
    public function contains($field, $value)
    {
        return new Comparison\Contains($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\MemberOf
     */
    public function memberOf($field, $value)
    {
        return new Comparison\MemberOf($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\StartsWith
     */
    public function startsWith($field, $value)
    {
        return new Comparison\StartsWith($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return Comparison\EndsWith
     */
    public function endsWith($field, $value)
    {
        return new Comparison\EndsWith($field, $value);
    }
}
