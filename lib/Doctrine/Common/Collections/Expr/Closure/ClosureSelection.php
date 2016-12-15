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
use Doctrine\Common\Collections\Expr\Selection;

/**
 * @author Oleksandr Sova <sovaalexandr@gmail.com>
 */
final class ClosureSelection extends Selection implements MatchByClosure
{
    /**
     * @return Filterable
     */
    public function andX()
    {
        $container = new ClosureAnd();
        $this->withContainer($container);

        return $container;
    }

    /**
     * @return Filterable
     */
    public function orX()
    {
        $container = new ClosureOr();
        $this->withContainer($container);

        return $container;
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function eq($field, $value)
    {
        $this->withContainer(new ClosureEqual($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function neq($field, $value)
    {
        $this->withContainer(new ClosureNotEqual($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function contains($field, $value)
    {
        $this->withContainer(new ClosureContains($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function startsWith($field, $value)
    {
        $this->withContainer(new ClosureStartsWith($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function endsWith($field, $value)
    {
        $this->withContainer(new ClosureEndsWith($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function greaterThan($field, $value)
    {
        $this->withContainer(new ClosureGreaterThan($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function greaterThanEqual($field, $value)
    {
        $this->withContainer(new ClosureGreaterThanEqual($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function lessThan($field, $value)
    {
        $this->withContainer(new ClosureLowerThan($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function lessThanEqual($field, $value)
    {
        $this->withContainer(new ClosureLowerThanEqual($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function in($field, $value)
    {
        $this->withContainer(new ClosureIn($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function notIn($field, $value)
    {
        $this->withContainer(new ClosureNotIn($field, $value));
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function memberOf($field, $value)
    {
        $this->withContainer(new ClosureMemberOf($field, $value));
    }

    /**
     * @param callable $closure
     */
    public function matchBy($closure)
    {
        $this->withContainer(new ClosureMatchClosure($closure));
    }
}
