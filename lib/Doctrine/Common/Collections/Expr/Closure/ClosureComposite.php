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

namespace Doctrine\Common\Collections\Expr\Closure;

use Doctrine\Common\Collections\Expr\Filterable;
use Doctrine\Common\Collections\Expr\FilterAware;

/**
 * @author Oleksandr Sova <sovaalexandr@gmail.com>
 */
abstract class ClosureComposite implements FilterAware, Filterable
{
    /**
     * @var FilterAware[]
     */
    private $expressions = [];

    /**
     * @return \Closure
     */
    abstract public function getFilter();

    /**
     * @return Filterable
     */
    final public function andX()
    {
        $container = new ClosureAnd();
        $this->expressions[] = $container;

        return $container;
    }

    /**
     * @return Filterable
     */
    public function orX()
    {
        $container = new ClosureOr();
        $this->expressions[] = $container;

        return $container;
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    final public function eq($field, $value)
    {
        $this->expressions[] = new ClosureEqual($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function neq($field, $value)
    {
        $this->expressions[] = new ClosureNotEqual($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function contains($field, $value)
    {
        $this->expressions[] = new ClosureContains($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function startsWith($field, $value)
    {
        $this->expressions[] = new ClosureStartsWith($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function endsWith($field, $value)
    {
        $this->expressions[] = new ClosureEndsWith($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function greaterThan($field, $value)
    {
        $this->expressions[] = new ClosureGreaterThan($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function greaterThanEqual($field, $value)
    {
        $this->expressions[] = new ClosureGreaterThanEqual($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function lessThan($field, $value)
    {
        $this->expressions[] = new ClosureLowerThan($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function lessThanEqual($field, $value)
    {
        $this->expressions[] = new ClosureLowerThanEqual($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function in($field, $value)
    {
        $this->expressions[] = new ClosureIn($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function notIn($field, $value)
    {
        $this->expressions[] = new ClosureNotIn($field, $value);
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function memberOf($field, $value)
    {
        $this->expressions[] = new ClosureMemberOf($field, $value);
    }

    /**
     * @return FilterAware[]
     */
    final protected function getExpressions()
    {
        return $this->expressions;
    }
}
