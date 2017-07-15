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

namespace Doctrine\Common\Collections\Expr;

/**
 * Comparison of a field with a value by the given operator.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since  2.3
 */
class Comparison implements Expression
{
    public const EQ           = '=';
    public const NEQ          = '<>';
    public const LT           = '<';
    public const LTE          = '<=';
    public const GT           = '>';
    public const GTE          = '>=';
    public const IS           = '='; // no difference with EQ
    public const IN           = 'IN';
    public const NIN          = 'NIN';
    public const CONTAINS     = 'CONTAINS';
    public const MEMBER_OF    = 'MEMBER_OF';
    public const STARTS_WITH  = 'STARTS_WITH';
    public const ENDS_WITH    = 'ENDS_WITH';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $op;

    /**
     * @var Value
     */
    private $value;

    /**
     * @param string $field
     * @param string $operator
     * @param mixed  $value
     */
    public function __construct(string $field, string $operator, $value)
    {
        if ( ! ($value instanceof Value)) {
            $value = new Value($value);
        }

        $this->field = $field;
        $this->op = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->op;
    }

    /**
     * {@inheritDoc}
     */
    public function visit(ExpressionVisitor $visitor)
    {
        return $visitor->walkComparison($this);
    }
}
